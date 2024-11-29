<?php

return [
    "wordCounts" => [
        "registrar_visita" => [
            "registrar" => 3,
            "visita" => 4,
            "nueva" => 2,
            "agendar" => 3,
            "cita" => 2,
            "entrada" => 2,
            "trabajador" => 2,
        ],
        "listar_visitas" => [
            "quiero" => 1,
            "listar" => 3,
            "mostrar" => 2,
            "las" => 1,
            "visitas" => 4,
            "activas" => 2,
            "ver" => 3,
            "todas" => 2,
        ],
        "registrar_receso" => [
            "registrar" => 3,
            "receso" => 5,
            "nuevo" => 2,
            "pausa" => 3,
            "descanso" => 3,
            "trabajador" => 2,
        ],
        "listar_recesos" => [
            "listar" => 3,
            "recesos" => 4,
            "ver" => 3,
            "activos" => 2,
            "mostrar" => 2,
            "todas" => 1,
        ],
        "saludo" => [
            "hola" => 3,
            "buenos" => 2,
            "dias" => 2,
            "tardes" => 2,
            "saludos" => 2,
            "hey" => 1,
            "que" => 1,
            "tal" => 1,
        ],
    ],
    "intentCounts" => [
        "registrar_visita" => 5,
        "listar_visitas" => 5,
        "registrar_receso" => 5,
        "listar_recesos" => 5,
        "saludo" => 3,
    ],
    "vocab" => [
        "registrar", "visita", "nueva", "agendar", "cita", "entrada",
        "trabajador", "listar", "mostrar", "las", "visitas", "activas",
        "ver", "todas", "receso", "nuevo", "pausa", "descanso", "hola",
        "buenos", "dias", "tardes", "saludos", "hey", "que", "tal",
    ],
    "stopWords" => [
        "por", "favor", "quiero", "de", "un", "una", "el", "la", "los", "las", 
        "y", "o", "en", "a",
    ],
];
