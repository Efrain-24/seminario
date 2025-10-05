<?php
return [
    // % de mortalidad en un día para disparar alerta
    'mortalidad_umbral_porcentual' => 5, // 5%

    // peces/m3 máximos recomendados
    'densidad_umbral_critico' => 30, // ejemplo

    // canal de notificación por defecto
    'notificacion_canal' => ['database'], // ['database','mail'] si quieres correo
];
