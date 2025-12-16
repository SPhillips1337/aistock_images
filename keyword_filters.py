# Enhanced content filtering for stock photo generation
# Blocks inappropriate, unethical, or morally questionable content

def is_keyword_appropriate(keyword):
    """
    Filter out inappropriate or ethically problematic keywords before image generation.
    Returns True if appropriate, False if should be filtered out.
    """
    
    # Convert to lowercase for comparison
    keyword_lower = keyword.lower()
    
    # Categories of inappropriate content
    inappropriate_categories = {
        # Violence and harm
        'violence': ['shooting', 'gun', 'murder', 'kill', 'attack', 'violence', 'terror', 'war', 'crime'],
        'trauma': ['accident', 'crash', 'disaster', 'tragedy', 'emergency', 'police response', 'crime scene'],
        'drugs': ['drug', 'overdose', 'addiction', 'substance abuse'],
        'self_harm': ['suicide', 'self-harm', 'depression crisis'],
        
        # Inappropriate content
        'sexual': ['nude', 'sexual', 'adult', 'porn', 'erotic'],
        'hate': ['racist', 'hate', 'discrimination', 'extremist'],
        
        # Distressing events
        'medical_emergency': ['hospital emergency', 'medical crisis', 'injury', 'ambulance'],
        'death': ['death', 'dying', 'funeral', 'mortality'],
        
        # Illegal activities
        'illegal': ['theft', 'robbery', 'fraud', 'illegal', 'criminal'],
        
        # Bad taste/socially inappropriate
        'bad_taste': ['bondi beach shooting', 'tragedy tourism', 'disaster porn', 'shock content'],
    }
    
    # Check against each category
    for category, bad_words in inappropriate_categories.items():
        for bad_word in bad_words:
            if bad_word in keyword_lower:
                print(f"KEYWORD FILTERED: '{keyword}' contains inappropriate term '{bad_word}' (category: {category})")
                return False
    
    # Additional checks for context that might be problematic
    problematic_patterns = [
        'police response',  # Often indicates crime scenes
        'public reaction to',  # Often disasters/tragedies  
        'speeding ticket',   # Legal issues, not professional content
        'fine notice',       # Legal penalties
        'celebrity health',  # Privacy/inappropriate medical content
    ]
    
    for pattern in problematic_patterns:
        if pattern in keyword_lower:
            print(f"KEYWORD FILTERED: '{keyword}' contains problematic pattern '{pattern}'")
            return False
    
    print(f"KEYWORD APPROVED: '{keyword}' passed ethical filtering")
    return True

def filter_keywords(keywords):
    """
    Filter a list of keywords, returning only appropriate ones.
    """
    filtered_keywords = []
    filtered_out = []
    
    for keyword in keywords:
        if is_keyword_appropriate(keyword):
            filtered_keywords.append(keyword)
        else:
            filtered_out.append(keyword)
    
    if filtered_out:
        print(f"FILTERED OUT {len(filtered_out)} inappropriate keywords: {filtered_out}")
    print(f"APPROVED {len(filtered_keywords)} keywords for image generation")
    
    return filtered_keywords