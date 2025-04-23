<!-- resources/views/store/inactive.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Inactive - {{ $reseller->store_name }}</title>
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .inactive-container {
            max-width: 600px;
            padding: 40px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .store-logo {
            max-width: 100px;
            margin-bottom: 20px;
        }
        
        .text-primary-custom {
            color: {{ $reseller->store_theme_color ?? '#3490dc' }};
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="inactive-container">
            @if($reseller->store_logo)
                <img src="{{ asset('storage/' . $reseller->store_logo) }}" alt="{{ $reseller->store_name }}" class="store-logo">
            @else
                <i class="fas fa-store text-primary-custom fa-5x mb-4"></i>
            @endif
            
            <h1 class="mb-4">{{ $reseller->store_name }}</h1>
            
            <div class="alert alert-warning mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>This store is currently inactive</strong>
            </div>
            
            <p class="mb-4">The store owner's membership has expired. Please check back later when the store has been reactivated.</p>
            
            <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-home me-2"></i> Back to Home
            </a>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>