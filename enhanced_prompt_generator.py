#!/usr/bin/env /home/stephen/private/happymonkey.ai/stock_venv/bin/python3

# Enhanced prompt generator for stock photography based on Google Gemini's recommendations
# Uses camera specs, lighting, and material descriptions instead of basic quality terms

import random

class EnhancedPromptGenerator:
    """Generates professional stock photo prompts optimized for Z-Image Turbo model."""
    
    def __init__(self):
        # Camera settings that influence style without explicitly mentioning equipment
        self.camera_qualities = {
            "portrait": {
                "depth_effects": ["shallow depth of field", "beautiful bokeh", "subject separation"],
                "sharpness_effects": ["razor-sharp focus on eyes", "crisp detail rendering", "precise focus"],
                "field_effects": ["85mm compression", "flattering perspective", "intimate feel"]
            },
            "lifestyle": {
                "depth_effects": ["natural depth", "environmental context", "balanced focus"],
                "sharpness_effects": ["authentic focus", "candid sharpness", "selective focus"],
                "field_effects": ["35mm perspective", "environmental storytelling", "natural viewpoint"]
            },
            "commercial": {
                "depth_effects": ["front-to-back sharpness", "hyperfocal focus", "maximum sharpness"],
                "sharpness_effects": ["tack sharp", "commercial-grade sharpness", "crisp rendering"],
                "field_effects": ["professional perspective", "balanced composition", "technical precision"]
            },
            "architecture": {
                "depth_effects": ["infinite focus", "everything sharp", "depth of field from front to back"],
                "sharpness_effects": ["architectural sharpness", "technical precision", "structural clarity"],
                "field_effects": ["wide perspective", "corrected perspective", "professional architecture view"]
            },
            "nature": {
                "depth_effects": ["natural subject separation", "wildlife compression", "balanced depth"],
                "sharpness_effects": ["nature sharpness", "wildlife focus", "outdoor clarity"],
                "field_effects": ["natural perspective", "telephoto compression", "authentic wildlife view"]
            }
        }
        
        self.lighting_setups = {
            "natural": [
                "soft morning sunlight filtering through windows",
                "golden hour lighting creating warm rim light",
                "natural light with soft shadows",
                "overcast natural light for even illumination",
                "late afternoon sun creating dramatic shadows",
                "dappled sunlight through trees"
            ],
            "studio": [
                "soft studio lighting with octabox",
                "three-point lighting setup",
                "Rembrandt lighting pattern",
                "butterfly lighting for portraits",
                "softbox lighting creating gradual falloff",
                "ring light for subtle catchlights"
            ],
            "dramatic": [
                "dramatic side lighting creating deep shadows",
                "backlit with rim lighting",
                "low key lighting with high contrast",
                "cinematic lighting with blue and orange color grade",
                "volumetric lighting cutting through haze",
                "dramatic uplighting from below"
            ],
            "atmospheric": [
                "ethereal atmospheric lighting with volumetric fog",
                "soft-focus background with moody lighting",
                "neon lights creating color contrasts",
                "candlelight creating warm intimate atmosphere",
                "moonlight with cool blue tones",
                "firelight creating flickering warm light"
            ]
        }
        
        self.material_descriptions = {
            "modern": ["minimalist marble", "polished concrete", "brushed steel", "sleek glass", "matte black surfaces", "chrome accents"],
            "natural": ["weathered wood", "natural stone", "teak wood", "slate tiles", "bamboo flooring", "granite countertops"],
            "textiles": ["fine linen", "premium cotton", "luxurious velvet", "sheer silk", "chunky wool", "textured fabric"],
            "luxury": ["polished brass", "marble inlay", "exotic wood veneer", "crystal clear glass", "brushed gold", "fine leather"],
            "industrial": ["raw concrete", "exposed brick", "corrugated metal", "distressed wood", "galvanized steel", "iron fixtures"],
            "outdoor": ["natural grass", "weathered decking", "stone pavers", "gravel pathways", "natural foliage", "earth tones"]
        }
        
        self.enhancement_qualities = [
            "visible skin pores", "authentic expression", "natural pose", "candid moment", 
            "rich textures", "micro-contrast", "three-dimensional feel", "luminous quality",
            "subtle color grading", "film-like rendition", "organic look", "emotive atmosphere"
        ]

    def get_category_type(self, keyword):
        """Determine the category type based on keyword."""
        keyword_lower = keyword.lower()
        
        if any(word in keyword_lower for word in ['portrait', 'fashion', 'beauty', 'wedding', 'family', 'people', 'celebrity']):
            return 'portrait'
        elif any(word in keyword_lower for word in ['lifestyle', 'recreation', 'vacation', 'travel', 'party', 'celebration']):
            return 'lifestyle'
        elif any(word in keyword_lower for word in ['business', 'corporate', 'technology', 'science', 'computer', 'workplace']):
            return 'commercial'
        elif any(word in keyword_lower for word in ['architecture', 'interiors', 'buildings', 'cityscapes', 'real estate']):
            return 'architecture'
        elif any(word in keyword_lower for word in ['nature', 'landscapes', 'forests', 'mountains', 'beach', 'wildlife', 'animals']):
            return 'nature'
        else:
            return 'commercial'  # default for general stock photography

    def get_lighting_style(self, keyword):
        """Determine appropriate lighting style based on keyword."""
        keyword_lower = keyword.lower()
        
        if any(word in keyword_lower for word in ['wellness', 'spa', 'meditation', 'relaxation', 'zen', 'mindfulness']):
            return 'atmospheric'
        elif any(word in keyword_lower for word in ['corporate', 'business', 'professional', 'office']):
            return 'studio'
        elif any(word in keyword_lower for word in ['dramatic', 'cinematic', 'surreal', 'cyberpunk', 'contrast']):
            return 'dramatic'
        else:
            return 'natural'

    def get_material_style(self, keyword):
        """Determine appropriate material descriptions based on keyword."""
        keyword_lower = keyword.lower()
        
        if any(word in keyword_lower for word in ['modern', 'minimalist', 'contemporary', 'technology', 'cyberpunk']):
            return 'modern'
        elif any(word in keyword_lower for word in ['natural', 'eco', 'organic', 'wellness', 'zen']):
            return 'natural'
        elif any(word in keyword_lower for word in ['luxury', 'fashion', 'wedding', 'celebrity', 'premium']):
            return 'luxury'
        elif any(word in keyword_lower for word in ['industrial', 'urban', 'street', 'converted']):
            return 'industrial'
        elif any(word in keyword_lower for word in ['outdoor', 'travel', 'beach', 'nature', 'landscape']):
            return 'outdoor'
        else:
            return random.choice(['modern', 'natural', 'luxury'])

    def generate_enhanced_prompt(self, keyword):
        """Generate an enhanced professional stock photo prompt."""
        
        # Determine the style components
        category_type = self.get_category_type(keyword)
        lighting_style = self.get_lighting_style(keyword)
        material_style = self.get_material_style(keyword)
        
        # Get random components for this style
        camera_setup = self.camera_qualities[category_type]
        lighting_options = self.lighting_setups[lighting_style]
        material_options = self.material_descriptions[material_style]
        
        # Select specific components (camera qualities, not equipment)
        depth_effect = random.choice(camera_setup['depth_effects'])
        sharpness_effect = random.choice(camera_setup['sharpness_effects'])
        field_effect = random.choice(camera_setup['field_effects'])
        lighting = random.choice(lighting_options)
        material = random.choice(material_options)
        enhancement = random.choice(self.enhancement_qualities)
        
        # Get specific category contexts
        context = self._get_category_context(keyword)
        
        # Build the enhanced prompt (without explicit camera references)
        prompt_parts = [
            f"A professional stock photo of {keyword}",
            context,
            f"set in a space with {material} elements",
            f"{lighting}",
            f"{depth_effect}, {sharpness_effect}, {field_effect}",
            f"showing {enhancement}",
            "commercial lifestyle photography",
            "high-end production value",
            "authentic atmosphere"
        ]
        
        # Remove any empty parts and join
        prompt_parts = [part for part in prompt_parts if part and part.strip()]
        enhanced_prompt = ", ".join(prompt_parts)
        
        return enhanced_prompt

    def _get_category_context(self, keyword):
        """Get specific context based on category keywords."""
        keyword_lower = keyword.lower()
        
        contexts = {
            # Celebrity & Wellness (based on Gemini's examples)
            'wellness': "capturing a moment of self-care and healthy living, authentic expression of wellbeing",
            'celebrity': "with charismatic personality and professional poise, natural relaxed expression",
            'meditation': "practicing mindfulness with peaceful expression and serene atmosphere",
            'spa': "enjoying luxurious relaxation treatments and wellness rituals",
            'retreat': "experiencing a peaceful escape from daily stress",
            
            # Fitness & Sports
            'fitness': "during an authentic workout session with genuine exertion, visible sweat on skin",
            'sports': "in dynamic action pose with athletic grace and determination",
            'recreation': "enjoying leisure activities with genuine joy and relaxation",
            'training': "focused on skill development with professional form and dedication",
            
            # Business & Corporate
            'business': "in a professional corporate environment with modern office setup",
            'corporate': "showcasing leadership and professional expertise in business setting",
            'meeting': "engaged in collaborative business discussion with authentic interaction",
            'workplace': "demonstrating productivity in modern office environment",
            'career': "showing professional development and workplace success",
            
            # Technology & Science
            'technology': "interacting with modern digital devices with natural gestures",
            'computer': "working with cutting-edge technology with focused concentration",
            'science': "conducting research in laboratory setting with professional equipment",
            'space': "exploring cosmic environments with wonder and discovery",
            'ai': "interacting with artificial intelligence in futuristic setting",
            
            # Nature & Environment
            'nature': "immersed in natural outdoor settings with authentic appreciation",
            'landscape': "capturing breathtaking natural scenery with dramatic lighting",
            'forest': "wandering through ancient woodland with dappled sunlight",
            'mountain': "conquering majestic peaks with outdoor adventure spirit",
            'beach': "enjoying coastal paradise with ocean breeze and warm sand",
            'wildlife': "observing animals in their natural habitat with respectful distance",
            
            # Architecture & Urban
            'architecture': "showcasing impressive structural design and geometric precision",
            'cityscapes': "viewing urban landscapes from elevated perspective with city lights",
            'street': "capturing authentic urban life with candid moments",
            'interiors': "experiencing beautifully designed indoor spaces with luxury details",
            'modern': "featuring contemporary design with clean lines and minimalist aesthetic",
            
            # People & Lifestyle
            'portrait': "capturing genuine personality with emotional depth and authentic expression",
            'lifestyle': "living authentically in everyday moments of genuine connection",
            'family': "sharing tender family moments with natural affection and joy",
            'fashion': "wearing contemporary designer clothing with confident pose",
            'beauty': "showcasing natural beauty with authentic radiance and grace",
            
            # Food & Drink
            'food': "presenting culinary excellence with artistic plating and fresh ingredients",
            'drink': "enjoying crafted beverages with satisfying refreshment",
            'restaurant': "dining in elegant atmosphere with gourmet presentation",
            'coffee': "savoring artisan coffee in cozy cafe environment",
            
            # Travel & Vacation
            'travel': "discovering new destinations with sense of wonder and adventure",
            'vacation': "enjoying perfect holiday moments with relaxation and joy",
            'destination': "experiencing iconic locations with photogenic quality",
            
            # Events & Celebrations
            'wedding': "celebrating marriage with romantic moments and genuine emotion",
            'party': "enjoying festive celebration with joy and social connection",
            'celebration': "marking special occasions with authentic happiness",
            'holiday': "experiencing seasonal traditions with warmth and nostalgia",
            
            # Artistic & Creative
            'surreal': "creating dreamlike imagery with imaginative concepts",
            'cinematic': "capturing movie-like scenes with dramatic lighting and composition",
            'abstract': "expressing artistic concepts through geometric forms and color",
            'minimalist': "showcasing simple elegance with clean composition",
            'experimental': "pushing creative boundaries with innovative techniques"
        }
        
        for key, context in contexts.items():
            if key in keyword_lower:
                return context
        
        return "in a carefully composed professional setting"

    def generate_ovis_fallback_prompt(self, keyword, text_issue_reason=""):
        """Generate Ovis-specific fallback prompt for text issues based on Gemini's tip."""
        
        # Determine the category type for appropriate text placement
        category_type = self.get_category_type(keyword)
        
        # Define text placement and style based on category
        text_configs = {
            'portrait': {
                'position': 'centered at top',
                'font_style': 'clean, bold, white sans-serif font',
                'background_style': 'minimalist professional background'
            },
            'lifestyle': {
                'position': 'subtly placed in lower right corner', 
                'font_style': 'elegant, clean white sans-serif font',
                'background_style': 'lifestyle environment with complementary colors'
            },
            'commercial': {
                'position': 'prominently displayed in upper third',
                'font_style': 'professional, bold, white sans-serif font',
                'background_style': 'modern business environment with clean composition'
            },
            'architecture': {
                'position': 'integrated into composition naturally',
                'font_style': 'clean, architectural white font',
                'background_style': 'structured architectural setting'
            },
            'nature': {
                'position': 'placed harmoniously within scene',
                'font_style': 'natural, organic white sans-serif font', 
                'background_style': 'natural outdoor setting with complementary elements'
            }
        }
        
        # Default to commercial config for general stock photography
        config = text_configs.get(category_type, text_configs['commercial'])
        
        # Create clean text from keyword (remove special characters)
        clean_text = keyword.replace(' and ', ' & ').replace(' ', ' ').upper()
        
        # Add specific context based on keyword
        category_context = self._get_ovis_context(keyword)
        
        # Build Ovis fallback prompt
        ovis_prompt_parts = [
            f"A professional stock photo of {category_context}",
            f"The text '{clean_text}' is written in a {config['font_style']} {config['position']}",
            f"High-end lighting with clean composition",
            f"{config['background_style']}",
            f"Text is clearly visible and professionally integrated",
            f"Commercial stock photography with polished appearance",
            f"Sharp focus on both subject and text"
        ]
        
        return ", ".join(ovis_prompt_parts)
    
    def get_ovis_fallback_prompt(self, keyword, text_issue_reason=""):
        """Generate Ovis-specific fallback prompt for text issues based on Gemini's tip."""
        
        # Determine category type for appropriate text placement
        category_type = self.get_category_type(keyword)
        
        # Define text placement and style based on category
        text_configs = {
            'portrait': {
                'position': 'centered at top',
                'font_style': 'clean, bold, white sans-serif font',
                'background_style': 'minimalist professional background'
            },
            'lifestyle': {
                'position': 'subtly placed in lower right corner', 
                'font_style': 'elegant, clean white sans-serif font',
                'background_style': 'lifestyle environment with complementary colors'
            },
            'commercial': {
                'position': 'prominently displayed in upper third',
                'font_style': 'professional, bold, white sans-serif font',
                'background_style': 'modern business environment with clean composition'
            },
            'architecture': {
                'position': 'integrated into composition naturally',
                'font_style': 'clean, architectural white font',
                'background_style': 'structured architectural setting'
            },
            'nature': {
                'position': 'placed harmoniously within scene',
                'font_style': 'natural, organic white sans-serif font', 
                'background_style': 'natural outdoor setting with complementary elements'
            }
        }
        
        # Default to commercial config for general stock photography
        config = text_configs.get(category_type, text_configs['commercial'])
        
        # Create clean text from keyword (remove special characters)
        clean_text = keyword.replace(' and ', ' & ').replace(' ', ' ').upper()
        
        # Add specific context based on keyword
        category_context = self._get_ovis_context(keyword)
        
        # Build Ovis fallback prompt
        ovis_prompt_parts = [
            f"A professional stock photo of {category_context}",
            f"The text '{clean_text}' is written in a {config['font_style']} {config['position']}",
            f"High-end lighting with clean composition",
            f"{config['background_style']}",
            f"Text is clearly visible and professionally integrated",
            f"Commercial stock photography with polished appearance",
            f"Sharp focus on both subject and text"
        ]
        
        return ", ".join(ovis_prompt_parts)
    
    def _get_ovis_context(self, keyword):
        """Get Ovis-specific context that works well with text integration."""
        keyword_lower = keyword.lower()
        
        # Ovis contexts optimized for text placement
        ovis_contexts = {
            'wellness': "person practicing yoga with serene expression",
            'fitness': "athlete in professional workout pose with gym background", 
            'business': "professional in modern office setting",
            'technology': "person using modern digital devices",
            'nature': 'beautiful natural landscape with clear sky area',
            'architecture': 'modern building with clean wall space for text',
            'lifestyle': 'person enjoying lifestyle activity with clean background',
            'portrait': 'professional model with neutral expression',
            'food': 'beautifully arranged food with clean surface for text',
            'travel': 'travel destination with clear sky for text placement'
        }
        
        for key, context in ovis_contexts.items():
            if key in keyword_lower:
                return context
        
        return "professional subject with clean background"

    def generate_multiple_variants(self, keyword, num_variants=3):
        """Generate multiple prompt variants for testing."""
        variants = []
        for i in range(num_variants):
            variant = self.generate_enhanced_prompt(keyword)
            variants.append(variant)
        return variants

def test_prompt_generator():
    """Test the enhanced prompt generator."""
    generator = EnhancedPromptGenerator()
    
    test_keywords = [
        "Celebrity Health and Wellness",
        "Business Technology",
        "Nature Landscape",
        "Fashion Photography",
        "Corporate Meeting",
        "Meditation Retreat"
    ]
    
    for keyword in test_keywords:
        print(f"\n=== {keyword} ===")
        variants = generator.generate_multiple_variants(keyword, 3)
        for i, variant in enumerate(variants, 1):
            print(f"\nVariant {i}:")
            print(variant)
        print("-" * 80)

if __name__ == "__main__":
    test_prompt_generator()