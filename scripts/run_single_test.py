#!/usr/bin/env python3
import os, json, io
from urllib.parse import urlparse
from PIL import Image
import random
import websocket

import sys
sys.path.insert(0, os.getcwd())
from enhanced_prompt_generator import EnhancedPromptGenerator
from auto_stock_creator import generate_image_with_workflow, WORKFLOW_CONFIG, COMFYUI_CLIENT_ID

COMFYUI_URL = os.environ.get('COMFYUI_URL')
if not COMFYUI_URL:
    raise SystemExit('COMFYUI_URL not set')

# Load turbo workflow
with open(WORKFLOW_CONFIG['turbo']['file'], 'r') as f:
    turbo_workflow = json.load(f)

# Connect to ComfyUI websocket
ws = websocket.WebSocket()
ws.connect(f"ws://{urlparse(COMFYUI_URL).netloc}/ws?clientId={COMFYUI_CLIENT_ID}")

try:
    generator = EnhancedPromptGenerator()
    keyword = os.environ.get('TEST_KEYWORD', 'Minimalist Clean Composition')
    prompt = generator.generate_enhanced_prompt(keyword)
    print('Using prompt:', prompt)
    seed = random.randint(1, 10**12)
    images = generate_image_with_workflow(ws, turbo_workflow, prompt, seed, COMFYUI_CLIENT_ID, WORKFLOW_CONFIG['turbo']['nodes'])
    # Normalize images variable to a list of byte blobs
    blobs = []
    if isinstance(images, dict):
        for v in images.values():
            blobs.extend(v)
    elif isinstance(images, list):
        blobs = images
    else:
        blobs = list(images)

    print('Got', len(blobs), 'images')
    # Save first image if present
    if blobs:
        img = Image.open(io.BytesIO(blobs[0]))
        fname = f"images/test_{keyword.replace(' ','_')}_0.png"
        img.save(fname)
        print('Saved', fname)
    else:
        print('No images returned')
    raise SystemExit
finally:
    ws.close()
