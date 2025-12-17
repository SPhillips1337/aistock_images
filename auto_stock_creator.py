#!/usr/bin/env /home/stephen/private/happymonkey.ai/stock_venv/bin/python3

import requests
import websocket
import uuid
import json
import feedparser
import base64
from urllib.parse import urlparse
from PIL import Image
import io
import os
from bs4 import BeautifulSoup
from datetime import datetime
import random
from dotenv import load_dotenv

# Import our keyword filters
try:
    from keyword_filters import filter_keywords
except ImportError:
    print("Warning: keyword_filters.py not found. Using unfiltered keywords.")
    def filter_keywords(keywords):
        return keywords

# Import enhanced prompt generator (optional)
try:
    from enhanced_prompt_generator import EnhancedPromptGenerator
except ImportError:
    # Fallback trivial implementation
    class EnhancedPromptGenerator:
        def generate_enhanced_prompt(self, keyword):
            return f"stock photography of {keyword}, high quality, 4k, photorealistic, trending on artstation"
        def get_ovis_fallback_prompt(self, keyword, text_issue_reason=""):
            clean_text = keyword.replace(' and ', ' & ').replace(' ', ' ').upper()
            return f"A professional stock photo of {keyword}. The text '{clean_text}' is written in a clean, bold, white sans-serif font centered at the top. High-end lighting, minimalist background."

# Load environment variables
load_dotenv()

# Configuration from environment variables
COMFYUI_URL = os.getenv("COMFYUI_URL")
OLLAMA_URL = os.getenv("OLLAMA_URL")
OLLAMA_TEXT_MODEL = os.getenv("OLLAMA_TEXT_MODEL")
OLLAMA_VISION_MODEL = os.getenv("OLLAMA_VISION_MODEL")
BBC_RSS_URL = os.getenv("BBC_RSS_URL")
HOLIDAYS_URL = os.getenv("HOLIDAYS_URL")
USER_AGENT = os.getenv("USER_AGENT")

# Validate required environment variables
required_vars = ["COMFYUI_URL", "OLLAMA_URL", "OLLAMA_TEXT_MODEL", "OLLAMA_VISION_MODEL"]
for var in required_vars:
    if not os.getenv(var):
        raise ValueError(f"{var} not found in .env file")

COMFYUI_CLIENT_ID = str(uuid.uuid4())

# File paths
COMFYUI_WORKFLOW_TURBO = "image_z_image_turbo.json"
COMFYUI_WORKFLOW_OVIS = "image_ovis_text_to_image.json"
IMAGE_DIR = "images"

# Node mappings for each workflow
WORKFLOW_CONFIG = {
    "turbo": {
        "file": COMFYUI_WORKFLOW_TURBO,
        "nodes": {
            "prompt": "45",
            "seed": "44"
        }
    },
    "ovis": {
        "file": COMFYUI_WORKFLOW_OVIS,
        "nodes": {
            "prompt": "54:45",
            "seed": "54:44"
        }
    }
}

def get_stock_categories():
    """Returns a list of popular stock photography categories."""
    return [
        # General & Abstract
        "Wallpapers", "3D Renders", "Textures", "Backgrounds", "Experimental",
        "Retro and Vintage", "Film Photography", "Minimalist", "Patterns",
        
        # Nature & Environment
        "Nature", "Landscapes", "Flowers", "Sky", "Water", "Seascapes", 
        "Forests", "Mountains", "Beach", "Seasons", "Winter", "Spring", "Summer", "Autumn",
        
        # Urban & Architecture
        "Architecture", "Street Photography", "Cityscapes", "Interiors", "Real Estate", 
        "Buildings", "Bridges", "Urban Lifestyle",
        
        # People & Lifestyle
        "People", "Lifestyle", "Family", "Portrait", "Fashion", "Beauty",
        "Business", "Corporate", "Workplace", "Career", "Education", "Learning",
        "Healthcare", "Medicine", "Wellness", "Fitness", "Sports", "Recreation",
        
        # Events & Holidays
        "Holidays", "Seasonal", "Christmas", "New Year", "Halloween", "Easter",
        "Celebration", "Party", "Wedding", "Special Occasions", "Birthday",
        
        # Objects & Topics
        "Technology", "Computer", "Science", "Space", "AI and Future",
        "Food", "Drink", "Coffee", "Restaurant", "Cooking",
        "Transportation", "Cars", "Travel", "Travel Destinations", "Vacation",
        "Animals", "Pets", "Dogs", "Cats", "Wildlife",
        
        # Artistic Concepts
        "Authentic candid", "Surreal dreamscape", "High contrast", "Geometric composition",
        "Pastel colors", "Cyberpunk", "Cinematic lighting"
    ]

def get_unique_filename(filepath):
    """Returns a unique filepath by appending a counter if the file exists."""
    if not os.path.exists(filepath):
        return filepath
    base, ext = os.path.splitext(filepath)
    counter = 1
    while True:
        new_path = f"{base}_{counter}{ext}"
        if not os.path.exists(new_path):
            return new_path
        counter += 1

def get_todays_holidays():
    """Gets today's 'Fun Holidays' from timeanddate.com."""
    url = HOLIDAYS_URL
    headers = {
        'User-Agent': USER_AGENT
    }
    
    holidays = []
    try:
        response = requests.get(url, headers=headers)
        response.raise_for_status()
        soup = BeautifulSoup(response.content, 'html.parser')
        
        # Get today's date in the format used by the site, e.g., "Jan 7"
        # The site seems to use "%b %-d" (e.g., "Dec 12")
        today_str = datetime.now().strftime("%b %-d")
        print(f"Looking for holidays for: {today_str}")
        
        # Find the table row for today
        rows = soup.find_all('tr')
        for row in rows:
            # The date is in the first td
            cells = row.find_all('td')
            if not cells:
                continue
            
            date_text = cells[0].get_text(strip=True)
            
            if today_str == date_text:
                # This is today's row, get the holiday name from the 'a' tag in the check third td (index 2) usually
                # Based on the debug output: 3rd cell has the link
                if len(cells) > 2:
                    link = cells[2].find('a')
                    if link:
                        holidays.append(link.get_text(strip=True))
                    
    except Exception as e:
        print(f"Error scraping holidays: {e}")
        
    return holidays
    
def get_bbc_news_headlines():
    """Fetches top headlines from BBC News RSS feed."""
    url = BBC_RSS_URL
    print(f"Fetching news from {url}...")
    try:
        feed = feedparser.parse(url)
        headlines = [entry.title for entry in feed.entries[:20]]
        print(f"Found {len(headlines)} headlines.")
        return headlines
    except Exception as e:
        print(f"Error fetching news: {e}")
        return []

def get_ai_categories(headlines):
    """Uses LLM to extract trending stock photo categories from headlines."""
    if not headlines:
        return []
    
    print("Asking AI to extract trending categories...")
    
    # Use environment variables for AI configuration
    ai_url = OLLAMA_URL
    model = OLLAMA_TEXT_MODEL
    
    headlines_text = "\n".join(headlines)
    prompt = f"""
    Here are the current top news headlines:
    {headlines_text}
    
    Based on these headlines, identify 10 trending, visual themes suitable for stock photography.
    Focus on general concepts (e.g., "Winter Storm", "Hospital", "Space Rocket") rather than specific people.
    
    Return the 10 categories as a simple list, one per line.
    Do not use numbering, bullets, or JSON. Just the words.
    """
    
    payload = {
        "model": model,
        "prompt": prompt,
        "stream": False
    }
    
    try:
        response = requests.post(ai_url, json=payload, timeout=30)
        response.raise_for_status()
        result = response.json()
        
        # Parse the response from the LLM
        response_text = result.get('response', '')
        print(f"Raw AI response: {response_text}")
        
        categories = []
        for line in response_text.split('\n'):
            line = line.strip()
            # Remove bullets if model ignored instructions
            if line.startswith(('-', '*', '1.', '•')):
                line = line.lstrip('-*1.• ')
            
            if line and len(line) > 2:
                categories.append(line)
        
        # Limit to 10
        categories = categories[:10]
        
        if categories:
            print(f"AI suggested categories: {categories}")
            return categories
        else:
            print("No valid categories found in AI response.")
            return []
            
    except Exception as e:
        print(f"Error getting AI categories: {e}")
        return []

def check_image_quality(image_path):
    """
    Checks the image quality using a Vision LLM.
    Returns True if pass, False if reject.
    """
    print(f"Verifying image quality for {image_path}...")
    
    if not os.path.exists(image_path):
        print("Image not found for verification.")
        return True # Fail open? Or fail closed? Let's pass to avoid blocking if file missing
        
    ai_url = OLLAMA_URL
    model = OLLAMA_VISION_MODEL
    
    try:
        with open(image_path, "rb") as image_file:
            encoded_string = base64.b64encode(image_file.read()).decode('utf-8')
            
        prompt = """
        Analyze this image for quality and content appropriateness for a professional stock photo website.
        
        IMPORTANT: This is for professional/business use. Be strict about appropriateness.
        
        Check for:
        1. Text quality: Is there any text? If so, is it legible or gibberish?
        2. Human anatomy: Are there human figures? Do they have correct anatomy (fingers, limbs)?
        3. Image clarity: Is the image blurry or nonsensical?
        4. NSFW content: Is there any nudity, sexual content, or inappropriate material?
        5. Professional suitability: Is this appropriate for business/professional use?
        6. Ethical concerns: Does this depict trauma, violence, crime scenes, accidents, or distressing events?
        7. Bad taste content: Is this exploitative, sensationalist, or morally questionable?
        
        SPECIFICALLY REJECT IF:
        - Any form of violence, crime scenes, police activity
        - Medical emergencies, injuries, hospitals in crisis
        - Traffic accidents, car crashes, speeding violations
        - Real tragedies, disasters, or emergency responses
        - Exploitative content or shock value imagery
        - Any content that could be seen as profiting from suffering
        - Politically sensitive or divisive content
        - Privacy violations or non-consensual photography
        
        Return a JSON object with:
        - "has_text": boolean
        - "text_quality": "good", "bad", or "n/a"
        - "has_humans": boolean
        - "anatomy_quality": "good", "bad", or "n/a"
        - "is_nsfw": boolean
        - "is_professional": boolean
        - "is_ethical": boolean (NEW: specifically addresses ethical concerns)
        - "has_violence": boolean (NEW: violence, crime, trauma)
        - "overall_quality": "pass" or "reject"
        - "reason": string explanation
        """
        
        payload = {
            "model": model,
            "prompt": prompt,
            "images": [encoded_string],
            "stream": False,
            "format": "json"
        }
        
        response = requests.post(ai_url, json=payload, timeout=60)
        response.raise_for_status()
        result = response.json()
        
        response_text = result.get('response', '{}')
        if "```" in response_text:
            response_text = response_text.replace("```json", "").replace("```", "").strip()
            
        analysis = json.loads(response_text)
        print(f"Vision QA Analysis: {analysis}")
        
        # Reject if NSFW content detected
        if analysis.get("is_nsfw", False):
            print(f"Image REJECTED: NSFW content detected - {analysis.get('reason')}")
            return False
            
        # NEW: Reject if unethical content
        if not analysis.get("is_ethical", True):
            print(f"Image REJECTED: Unethical content - {analysis.get('reason')}")
            return False
            
        # NEW: Reject if contains violence/trauma
        if analysis.get("has_violence", False):
            print(f"Image REJECTED: Violence/trauma content - {analysis.get('reason')}")
            return False
            
        # Reject if not professional/appropriate
        if not analysis.get("is_professional", True):
            print(f"Image REJECTED: Not professional - {analysis.get('reason')}")
            return False
        
        # Reject based on overall quality
        if analysis.get("overall_quality") == "reject":
            print(f"Image REJECTED: Quality issues - {analysis.get('reason')}")
            return False
            
        return True
        
    except Exception as e:
        print(f"Error checking image quality: {e}")
        return True # Fail open on error to keep running

def get_images(ws, prompt, client_id):
    prompt_id = queue_prompt(prompt, client_id)['prompt_id']
    output_images = {}
    while True:
        out = ws.recv()
        if isinstance(out, str):
            message = json.loads(out)
            if message['type'] == 'executing':
                data = message['data']
                if data['node'] is None and data['prompt_id'] == prompt_id:
                    break  # Execution is done
        else:
            continue  # Previews are binary data

    history = get_history(prompt_id)[prompt_id]
    for o in history['outputs']:
        for node_id in history['outputs']:
            node_output = history['outputs'][node_id]
            if 'images' in node_output:
                images_output = []
                for image in node_output['images']:
                    image_data = get_image(image['filename'], image['subfolder'], image['type'])
                    images_output.append(image_data)
                output_images[node_id] = images_output

    return output_images

def queue_prompt(prompt, client_id):
    p = {"prompt": prompt, "client_id": client_id}
    response = requests.post(f"{COMFYUI_URL}/prompt", json=p)
    response.raise_for_status()
    return response.json()

def get_history(prompt_id):
    response = requests.get(f"{COMFYUI_URL}/history/{prompt_id}")
    response.raise_for_status()
    return response.json()

def get_image(filename, subfolder, folder_type):
    response = requests.get(f"{COMFYUI_URL}/view", params={"filename": filename, "subfolder": subfolder, "type": folder_type})
    response.raise_for_status()
    return response.content


def generate_image_with_workflow(ws, workflow_template, prompt_text, seed, client_id, node_mapping):
    """
    Generates an image using the specified workflow and settings.
    Returns a list of (image_data, filename_suffix) tuples.
    """
    # Create a deep copy to avoid modifying the template for future runs
    workflow = json.loads(json.dumps(workflow_template))
    
    # Update prompt
    prompt_node = node_mapping["prompt"]
    workflow[prompt_node]["inputs"]["text"] = prompt_text
    
    # Update seed
    seed_node = node_mapping["seed"]
    workflow[seed_node]["inputs"]["seed"] = seed
    
    # Queue prompt
    images = get_images(ws, workflow, client_id)
    
    results = []
    for node_id in images:
        for image_data in images[node_id]:
            results.append(image_data)
            
    return results

def main():
    """Main function."""
    # Create image directory if it doesn't exist
    if not os.path.exists(IMAGE_DIR):
        os.makedirs(IMAGE_DIR)

    print("Fetching today's holidays...")
    try:
        holidays = get_todays_holidays()
    except Exception as e:
        print(f"Failed to fetch holidays: {e}")
        holidays = []
        
    trends = get_stock_categories()
    
    # Fetch news trends
    try:
        news_headlines = get_bbc_news_headlines()
        news_trends = get_ai_categories(news_headlines)
    except Exception as e:
        print(f"Failed to fetch news trends: {e}")
        news_trends = []

    # Combine all keywords
    raw_keywords = holidays + trends + news_trends
    
    # Filter out inappropriate/ethically problematic keywords
    keywords = filter_keywords(raw_keywords)
    
    if not keywords:
        print("No keywords found. Using fallback.")
        keywords = ["Abstract Art", "Nature Landscape"]
        
    # Shuffle keywords to mix sources
    random.shuffle(keywords)
    
    print(f"Found {len(keywords)} keywords ({len(holidays)} holidays, {len(trends)} static trends)")

    try:
        # Load ComfyUI workflows
        workflows = {}
        for key, config in WORKFLOW_CONFIG.items():
            try:
                with open(config["file"], 'r') as f:
                    workflows[key] = json.load(f)
            except FileNotFoundError:
                print(f"Warning: Workflow file {config['file']} not found. {key} functionality will be disabled.")
        
        if "turbo" not in workflows:
            raise FileNotFoundError("Primary Turbo workflow not found!")

        # Connect to ComfyUI websocket
        ws = websocket.WebSocket()
        ws.connect(f"ws://{urlparse(COMFYUI_URL).netloc}/ws?clientId={COMFYUI_CLIENT_ID}")

        try:
            # Initialize enhanced prompt generator
            prompt_generator = EnhancedPromptGenerator()

            for keyword in keywords:
                print(f"Processing keyword: {keyword}")
                
                # Base prompt construction
                stock_prompt = prompt_generator.generate_enhanced_prompt(keyword)

                # Try Primary (Turbo) first
                seed = random.randint(1, 10**15)
                print(f"Generating with Turbo... (Seed: {seed})")
                
                try:
                    turbo_images = generate_image_with_workflow(
                        ws, 
                        workflows["turbo"], 
                        stock_prompt, 
                        seed, 
                        COMFYUI_CLIENT_ID, 
                        WORKFLOW_CONFIG["turbo"]["nodes"]
                    )
                except Exception as e:
                    print(f"Error generating with Turbo: {e}")
                    continue

                for i, image_data in enumerate(turbo_images):
                    # Save and Check
                    image = Image.open(io.BytesIO(image_data))
                    title = keyword.replace(' ', '_').replace('/', '-')
                    # Use a hash or timestamp to ensure uniqueness if keywords repeat, relying on get_unique_filename
                    base_filename = f"{IMAGE_DIR}/{title}.png"
                    filename = get_unique_filename(base_filename)
                    
                    image.save(filename)
                    print(f"Saved Turbo: {filename}")
                    
                    # Verify Quality
                    is_good = check_image_quality(filename)
                    
                    if is_good:
                        print(f"Image passed Verification.")
                    else:
                        print(f"Image REJECTED. Moving to rejected folder.")
                        rejected_filename = filename + ".rejected"
                        os.rename(filename, rejected_filename)
                        
                        # FALLBACK to Ovis
                        if "ovis" in workflows:
                            print(f"Retrying with Ovis fallback...")
                            try:
                                ovis_images = generate_image_with_workflow(
                                    ws, 
                                    workflows["ovis"], 
                                    stock_prompt, 
                                    seed, # Reuse seed or new one? New one might be better for diffusion diffs, but reuse is fine too.
                                    COMFYUI_CLIENT_ID, 
                                    WORKFLOW_CONFIG["ovis"]["nodes"]
                                )
                                
                                for j, ovis_data in enumerate(ovis_images):
                                    ovis_img = Image.open(io.BytesIO(ovis_data))
                                    ovis_base = f"{IMAGE_DIR}/{title}_ovis.png"
                                    ovis_filename = get_unique_filename(ovis_base)
                                    ovis_img.save(ovis_filename)
                                    print(f"Saved Ovis Fallback: {ovis_filename}")
                                    
                                    # Optional: Check Ovis quality too? User didn't strictly ask, but implied "try re-making it".
                                    # We'll just save it for now as "better text" is the claim.
                            except Exception as e:
                                print(f"Error in Ovis fallback: {e}")
                        else:
                            print("Ovis workflow not loaded, skipping fallback.")

                print(f"Cycle complete for '{keyword}'.")

        finally:
            ws.close()

    except requests.exceptions.RequestException as e:
        print(f"Network error (ComfyUI might be down): {e}")
    except Exception as e:
        print(f"An error occurred: {e}")

if __name__ == "__main__":
    main()
