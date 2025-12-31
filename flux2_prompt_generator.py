#!/usr/bin/env python3
"""
Flux2 LLM-Powered Prompt Generator

Uses the LLM to dynamically create rich, unique prompts optimized for Flux2.
"""

import requests
import os
from dotenv import load_dotenv

load_dotenv()

OLLAMA_URL = os.getenv("OLLAMA_URL")
OLLAMA_TEXT_MODEL = os.getenv("OLLAMA_TEXT_MODEL")


class Flux2PromptGenerator:
    """Generates creative stock photo prompts using LLM, optimized for Flux2."""
    
    def __init__(self):
        self.prompt_cache = {}
        self.llm_system_prompt = """You are a professional stock photography art director. 
Your job is to create vivid, detailed image prompts for the Flux2 AI image generator.

IMPORTANT GUIDELINES:
1. Write in natural, flowing sentences - NOT comma-separated lists
2. Describe a specific scene with rich visual details
3. Include: lighting quality, color palette, mood, composition, and atmosphere
4. Use style references like "editorial photography", "premium stock", "magazine quality"
5. Describe textures, materials, and environmental details
6. Avoid technical camera jargon (no f-stops, focal lengths, ISO)
7. Keep prompts between 50-100 words
8. Focus on creating commercially viable, professional stock imagery
9. Ensure content is appropriate for business use (SFW)

EXAMPLE OUTPUT:
"A serene morning yoga session in a sunlit studio with warm golden light streaming through floor-to-ceiling windows. A woman in comfortable athletic wear holds a graceful warrior pose, her expression peaceful and focused. Soft shadows play across the polished wooden floor. The atmosphere is calm and rejuvenating, with lush green plants adding natural energy. Premium editorial fitness photography with magazine-quality composition."
"""

    def generate_prompt(self, keyword, include_negatives=True):
        """Generate a rich, unique prompt for the given keyword using LLM."""
        
        # Check cache first
        cache_key = f"{keyword}_{include_negatives}"
        if cache_key in self.prompt_cache:
            print(f"Using cached prompt for: {keyword}")
            return self.prompt_cache[cache_key]
        
        print(f"Generating LLM prompt for: {keyword}")
        
        user_prompt = f"""Create a detailed image prompt for a professional stock photo with the theme: "{keyword}"

Remember:
- Write in natural sentences, not comma-separated lists
- Include specific visual details (lighting, colors, mood, composition)
- Make it commercially viable and professional
- 50-100 words

Return ONLY the prompt text, no explanations or formatting."""

        payload = {
            "model": OLLAMA_TEXT_MODEL,
            "prompt": user_prompt,
            "system": self.llm_system_prompt,
            "stream": False
        }
        
        try:
            response = requests.post(OLLAMA_URL, json=payload, timeout=60)
            response.raise_for_status()
            result = response.json()
            generated_prompt = result.get('response', '').strip()
            
            # Clean up the response (remove quotes if wrapped)
            if generated_prompt.startswith('"') and generated_prompt.endswith('"'):
                generated_prompt = generated_prompt[1:-1]
            
            # Add negative constraints if requested
            if include_negatives:
                negatives = (
                    " Avoid: extra limbs, deformed hands, extra fingers, blur, "
                    "artifacts, watermarks, logos, gibberish text, NSFW content."
                )
                generated_prompt = generated_prompt + negatives
            
            print(f"Generated prompt: {generated_prompt[:100]}...")
            
            # Cache the result
            self.prompt_cache[cache_key] = generated_prompt
            
            return generated_prompt
            
        except Exception as e:
            print(f"Error generating LLM prompt: {e}")
            # Fallback to simple template
            return self._fallback_prompt(keyword, include_negatives)
    
    def _fallback_prompt(self, keyword, include_negatives=True):
        """Simple fallback prompt if LLM fails."""
        prompt = (
            f"A professional stock photograph depicting {keyword}. "
            f"High-quality editorial photography with beautiful natural lighting, "
            f"rich colors, and premium commercial aesthetic. "
            f"Magazine-quality composition with attention to detail."
        )
        
        if include_negatives:
            prompt += (
                " Avoid: extra limbs, deformed hands, blur, artifacts, "
                "watermarks, logos, gibberish text."
            )
        
        return prompt
    
    def generate_enhanced_prompt(self, keyword, include_negatives=True):
        """Alias for compatibility with existing code."""
        return self.generate_prompt(keyword, include_negatives)
    
    def clear_cache(self):
        """Clear the prompt cache."""
        self.prompt_cache = {}


def test_generator():
    """Test the Flux2 prompt generator."""
    generator = Flux2PromptGenerator()
    
    test_keywords = [
        "Business Technology",
        "Nature Landscape",
        "Coffee Shop Lifestyle",
        "Fitness and Wellness",
        "Modern Architecture"
    ]
    
    for keyword in test_keywords:
        print(f"\n{'='*60}")
        print(f"Keyword: {keyword}")
        print(f"{'='*60}")
        prompt = generator.generate_prompt(keyword)
        print(f"\nGenerated Prompt:\n{prompt}")
        print()


if __name__ == "__main__":
    test_generator()
