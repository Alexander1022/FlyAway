# Flask App

This is a simple Flask application for classifying plants, animals, and mushrooms using custom TensorFlow models.

## 🚀 Quick Start

### 1. Activate the virtual environment
```bash
python -m venv venv
source venv/bin/activate
```

### 2. Install dependencies from the file
```bash
pip install -r requirements.txt
```

### 3. Run the Flask server
```bash
python3 pretrained.py
```

## API Endpoints

### Health Check
- **GET** `/health`
- **Response:**
  ```json
  { "status": "ok", "do-i-love-butterflies": "yes" }
  ```

### Predict
- **POST** `/predict?type=<model_type>`
- **Headers:**
  - `X-API-Key: <your_api_key>`
- **Form Data:**
  - `file`: image file to classify
- **Query Parameters:**
  - `type`: Which model to use. Options:
    - `plant` (default)
    - `animal`
    - `mushroom`
- **Response:**
  - On success:
    ```json
    {
      "type": "plant|animal|mushroom",
      "class_idx": <int>,
      "species_name": <str>,
      "confidence": <float>,
      "success": true
    }
    ```
  - On error:
    ```json
    {
      "error": <str>,
      "success": false
    }
    ```

## Example Usage

```bash
curl -X POST \
  -H "X-API-Key: <your_api_key>" \
  -F "file=@/path/to/image.jpg" \
  "http://localhost:5000/predict?type=animal"
```

Replace `<your_api_key>` with your actual API key.