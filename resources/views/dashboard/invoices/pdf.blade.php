<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Nota {{ $invoice->number }}</title>
<style>@page { margin: 14mm 12mm; } body { margin: 0; }</style>
</head>
<body>
@include('dashboard.invoices._sheet')
</body>
</html>
