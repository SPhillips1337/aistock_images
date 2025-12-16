
import unittest
from unittest.mock import MagicMock, patch
import sys
import os

# Add local directory to sys.path to import the script
sys.path.append(os.getcwd())

from auto_stock_creator import generate_image_with_workflow, WORKFLOW_CONFIG, COMFYUI_CLIENT_ID

class TestFallback(unittest.TestCase):
    @patch('auto_stock_creator.get_images')
    @patch('auto_stock_creator.websocket')
    def test_generate_image_call_structure(self, mock_ws, mock_get_images):
        # Setup mocks
        mock_ws_instance = MagicMock()
        mock_ws.WebSocket.return_value = mock_ws_instance
        
        # Mock what get_images returns (dict of node_id -> list of image bytes)
        mock_get_images.return_value = {'node_1': [b'fake_image_data']}
        
        # Test Turbo Config
        turbo_config = WORKFLOW_CONFIG['turbo']
        # Mock template
        template = {
            "45": {"inputs": {"text": "orig"}},
            "44": {"inputs": {"seed": 123}}
        }
        
        results = generate_image_with_workflow(
            mock_ws_instance, 
            template, 
            "new prompt", 
            999, 
            "client_id", 
            turbo_config['nodes']
        )
        
        # Verify result
        self.assertEqual(results, [b'fake_image_data'])
        
        # Verify generic function modified the correct nodes
        # The function loads/dumps json, so we check the call arguments to get_images
        args, _ = mock_get_images.call_args
        called_workflow = args[1] # 2nd arg is workflow
        
        self.assertEqual(called_workflow["45"]["inputs"]["text"], "new prompt")
        self.assertEqual(called_workflow["44"]["inputs"]["seed"], 999)

    @patch('auto_stock_creator.get_images')
    def test_ovis_config_mapping(self, mock_get_images):
        # Test that Ovis mapping works with the function
        ovis_config = WORKFLOW_CONFIG['ovis']
        template = {
            "54:45": {"inputs": {"text": "orig"}},
            "54:44": {"inputs": {"seed": 123}}
        }
        mock_get_images.return_value = {}
        
        generate_image_with_workflow(
            MagicMock(), 
            template, 
            "ovis prompt", 
            888, 
            "client_id", 
            ovis_config['nodes']
        )
        
        args, _ = mock_get_images.call_args
        called_workflow = args[1]
        
        self.assertEqual(called_workflow["54:45"]["inputs"]["text"], "ovis prompt")
        self.assertEqual(called_workflow["54:44"]["inputs"]["seed"], 888)

if __name__ == '__main__':
    unittest.main()
