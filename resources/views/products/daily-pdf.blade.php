<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remaining Documents</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 20px;
            line-height: 1.4;
        }
        
        .date {
            font-weight: bold;
            margin-bottom: 10px;
            color: #0066cc;
            font-size: 13px;
        }
        
        .document-list {
            margin-top: 5px;
        }
        
        .doc-item {
            margin-bottom: 5px;
            page-break-inside: avoid;
        }
        
        .doc-header {
            font-weight: normal;
            margin-bottom: 1px;
            font-size: 12px;
        }
        
        .doc-number {
            display: inline-block;
            width: 15px;
        }
        
        .doc-name {
            display: inline;
        }
        
        .batch-line {
            margin-left: 15px;
            margin-bottom: 1px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="date">Date: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</div>
    
    <div class="document-list">
        @if($groupedProducts->count() > 0)
            @foreach($groupedProducts as $index => $product)
                <div class="doc-item">
                    <div class="doc-header">
                        <span class="doc-number">{{ $index + 1 }}.</span>
                        <span class="doc-name">{{ $product['name'] }}</span>
                    </div>
                    @foreach($product['batches'] as $batch)
                        <div class="batch-line">{{ $batch['batch_no'] }} --------- {{ $batch['stage'] }}</div>
                    @endforeach
                </div>
            @endforeach
        @else
            <div style="text-align: center; margin-top: 30px;">No remaining documents found</div>
        @endif
    </div>
</body>
</html>
