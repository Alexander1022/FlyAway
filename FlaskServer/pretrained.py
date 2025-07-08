from flask import Flask, request, jsonify
import tensorflow as tf
import numpy as np
from PIL import Image
import json
import io
import os
from functools import wraps
import hmac
from dotenv import load_dotenv

load_dotenv()

app = Flask(__name__)
app.config['API_KEY'] = os.environ.get('API_KEY') 

classes = {'plant': None, 'animal': None, 'mushroom': None}
models = {'plant': None, 'animal': None, 'mushroom': None}

def initialize_service():
    global classes, models

    # Plant model
    with open('PlantAndFlowersModel/classes.json', 'r') as f:
        class_dict = json.load(f)
        plant_classes = [None] * len(class_dict)
        for k, v in class_dict.items():
            plant_classes[v] = k
        classes['plant'] = plant_classes
    models['plant'] = tf.saved_model.load('PlantAndFlowersModel')

    # Animal model
    with open('AnimalsModel/classes.json', 'r') as f:
        class_dict = json.load(f)
        animal_classes = [None] * len(class_dict)
        for k, v in class_dict.items():
            animal_classes[v] = k
        classes['animal'] = animal_classes
    models['animal'] = tf.saved_model.load('AnimalsModel')

    # Mushroom model
    with open('MushroomsModel/classes.json', 'r') as f:
        class_dict = json.load(f)
        mushroom_classes = [None] * len(class_dict)
        for k, v in class_dict.items():
            mushroom_classes[v] = k
        classes['mushroom'] = mushroom_classes
    models['mushroom'] = tf.saved_model.load('MushroomsModel')

initialize_service()

def preprocess_image(image):
    '''
    Ползвай ANTIALIAS, ако ползваш по-стара версия на PIL! 
    # img = image.resize((300, 300 * image.size[1] // image.size[0]), Image.ANTIALIAS)
    '''
    
    img = image.resize((300, 300 * image.size[1] // image.size[0]), Image.Resampling.LANCZOS)
    inp_numpy = np.array(img)[None]
    inp = tf.constant(inp_numpy, dtype=tf.float32)
    return inp

def require_api_key(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        provided_key = request.headers.get('X-API-Key')
        if not hmac.compare_digest(provided_key or '', app.config['API_KEY'] or ''):
            return jsonify({"error": "Unauthorized", "message": "Do you even have our secret key?"}), 401
        return f(*args, **kwargs)
    return decorated

@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'ok', 'do-i-love-butterflies': 'yes'})

@app.route('/predict', methods=['POST'])
@require_api_key
def predict():
    # The default model type is set to 'plant'
    model_type = request.args.get('type', 'plant')
    
    if model_type not in models:
        return jsonify({'error': 'Invalid type. Use "plant", "animal", or "mushroom".'}), 400

    if 'file' not in request.files:
        return jsonify({'error': 'No file uploaded'}), 400

    file = request.files['file']
    if file.filename == '':
        return jsonify({'error': 'Empty filename'}), 400

    try:
        image_bytes = file.read()
        image = Image.open(io.BytesIO(image_bytes)).convert("RGB")
        inp = preprocess_image(image)

        class_scores = models[model_type](inp)[0].numpy()
        predicted_class_idx = int(np.argmax(class_scores))
        confidence = float(class_scores[predicted_class_idx])
        species_name = classes[model_type][predicted_class_idx] if predicted_class_idx < len(classes[model_type]) else "Unknown"

        print(model_type, predicted_class_idx, species_name, confidence)
        
        return jsonify({
            'type': model_type,
            'class_idx': predicted_class_idx,
            'species_name': species_name,
            'confidence': confidence,
            'success': True
        })

    except Exception as e:
        return jsonify({
            'error': str(e),
            'success': False
        }), 500

if __name__ == '__main__':
    host = os.environ.get('APP_URL', 'http://flyaway-flask.localhost')
    if host.startswith(('http://', 'https://')):
        host = host.split('://', 1)[1]
    port = int(os.environ.get('APP_PORT', 5000))
    app.run(host=host, port=port, debug=False)