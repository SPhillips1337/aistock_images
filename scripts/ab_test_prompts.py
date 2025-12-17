#!/usr/bin/env python3
"""
A/B test for prompts: baseline vs negatives appended.
Generates N images per variant for a given keyword and reports QA pass counts.
"""
import os, sys, io, json, random
from urllib.parse import urlparse
from PIL import Image
import websocket

# ensure repo path
sys.path.insert(0, os.getcwd())

from enhanced_prompt_generator import EnhancedPromptGenerator
from auto_stock_creator import generate_image_with_workflow, WORKFLOW_CONFIG, COMFYUI_CLIENT_ID
from auto_stock_creator import check_image_quality as check_image_quality_detailed

COMFYUI_URL = os.environ.get('COMFYUI_URL')
if not COMFYUI_URL:
    print('COMFYUI_URL not set in env')
    sys.exit(1)

# Load turbo workflow
with open(WORKFLOW_CONFIG['turbo']['file'], 'r') as f:
    turbo_workflow = json.load(f)

keyword = os.environ.get('TEST_KEYWORD', 'Portrait')
N = int(os.environ.get('AB_TEST_N', '6'))

# Connect to ComfyUI websocket
ws = websocket.WebSocket()
ws.connect(f"ws://{urlparse(COMFYUI_URL).netloc}/ws?clientId={COMFYUI_CLIENT_ID}")

gen = EnhancedPromptGenerator()

results = {'baseline': {'attempts':0,'passes':0,'files':[]}, 'negatives': {'attempts':0,'passes':0,'files':[]}}

try:
    for variant in ['baseline','negatives']:
        for i in range(N):
            if variant == 'baseline':
                prompt = gen.generate_enhanced_prompt(keyword, include_negatives=False)
            else:
                prompt = gen.generate_enhanced_prompt(keyword, include_negatives=True)
            seed = random.randint(1,10**12)
            print(f"[{variant}] Iter {i+1}/{N} prompt: {prompt[:120]}...")
            images = generate_image_with_workflow(ws, turbo_workflow, prompt, seed, COMFYUI_CLIENT_ID, WORKFLOW_CONFIG['turbo']['nodes'])
            # normalize result
            blobs = []
            if isinstance(images, dict):
                for v in images.values():
                    blobs.extend(v)
            elif isinstance(images, list):
                blobs = images
            else:
                try:
                    blobs = list(images)
                except Exception:
                    blobs = []

            results[variant]['attempts'] += 1
            if not blobs:
                print('No images returned')
                continue
            # save first blob
            fname = f"images/ab_{variant}_{i+1}.png"
            Image.open(io.BytesIO(blobs[0])).save(fname)
            results[variant]['files'].append(fname)
            qa = check_image_quality_detailed(fname)
            passed = False
            reason = ''
            if isinstance(qa, dict):
                passed = bool(qa.get('pass'))
                reason = qa.get('reason','')
            else:
                passed = bool(qa)
            if passed:
                results[variant]['passes'] += 1
            print(f"Saved {fname} QA pass={passed} reason={reason}")

finally:
    ws.close()

print('\nA/B Test Summary:')
for v in ['baseline','negatives']:
    print(f"{v}: attempts={results[v]['attempts']} passes={results[v]['passes']} files={results[v]['files']}")

# Write results to file
with open('ab_test_results.json','w') as f:
    json.dump(results,f,indent=2)

print('Results written to ab_test_results.json')
