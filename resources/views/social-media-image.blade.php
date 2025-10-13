<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Imagen para Redes Sociales</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f9ff;
            width: 1080px;
            height: 1350px;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        
        .product-title {
            font-size: 48px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
        }
        
        .product-price {
            font-size: 36px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 15px;
        }
        
        .product-presentation {
            font-size: 24px;
            color: #6b7280;
            margin-bottom: 20px;
        }
        
        .product-location {
            font-size: 20px;
            color: #6b7280;
            margin-bottom: 15px;
        }
        
        .product-seller {
            font-size: 20px;
            color: #6b7280;
            margin-bottom: 30px;
        }
        
        .branding {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
        }
        
        .hashtags {
            font-size: 18px;
            color: #3b82f6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="product-title">{{ $listing->title ?? 'Producto' }}</h1>
        
        <div class="product-price">{{ $listing->formatted_price ?? '$0.00' }}</div>
        
        <div class="product-presentation">{{ $listing->formatted_presentation ?? 'Presentación' }}</div>
        
        <div class="product-location">📍 {{ $listing->location ?? 'Ubicación' }}</div>
        
        <div class="product-seller">👤 {{ $listing->person->name ?? 'Vendedor' }}</div>
        
        <div class="branding">EZEQUIELNEGOCIOS.COM</div>
        
        <div class="hashtags">#EzequielNegocios #Agricultura #Venezuela</div>
    </div>
</body>
</html>