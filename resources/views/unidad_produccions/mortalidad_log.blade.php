@extends("layouts.app")

@section("content")

<!-- Notificaciones -->
<x-notification type="success" :message="session(\"success\")" />
<x-notification type="error" :message="session(\"error\")" />
<x-notification type="warning" :message="session(\"warning\")" />

<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Log de Mortalidad</h1>
    <div class="bg-white shadow rounded p-6">
        <!-- Contenido del log aquí -->
    </div>
</div>

@endsection
