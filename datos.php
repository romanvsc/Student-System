<?php

//Archivo de datos del sistema de estudiantes

//Array principal de estudiantes

$estudiantes = [
    1 => [
        'id' => 1,
        'nombre' => 'Ana García López',
        'email' => 'ana.garcia@universidad.edu',
        'carrera' => 'Ingeniería en Sistemas',
        'semestre' => 6,
        'fecha_ingreso' => '2022-08-15',
        'telefono' => '+505 8234-5678',
        'direccion' => 'Managua, Nicaragua',
        'estado' => 'activo' // activo, inactivo, graduado
    ],
    2 => [
        'id' => 2,
        'nombre' => 'Carlos Mendoza Silva',
        'email' => 'carlos.mendoza@universidad.edu',
        'carrera' => 'Administración de Empresas',
        'semestre' => 4,
        'fecha_ingreso' => '2023-02-10',
        'telefono' => '+505 7765-4321',
        'direccion' => 'León, Nicaragua',
        'estado' => 'activo'
    ],
    3 => [
        'id' => 3,
        'nombre' => 'María Rodríguez Pérez',
        'email' => 'maria.rodriguez@universidad.edu',
        'carrera' => 'Medicina',
        'semestre' => 8,
        'fecha_ingreso' => '2021-08-20',
        'telefono' => '+505 8876-5432',
        'direccion' => 'Granada, Nicaragua',
        'estado' => 'activo'
    ],
    4 => [
        'id' => 4,
        'nombre' => 'José Herrera Castro',
        'email' => 'jose.herrera@universidad.edu',
        'carrera' => 'Ingeniería Civil',
        'semestre' => 2,
        'fecha_ingreso' => '2024-02-15',
        'telefono' => '+505 7543-2109',
        'direccion' => 'Masaya, Nicaragua',
        'estado' => 'activo'
    ],
    5 => [
        'id' => 5,
        'nombre' => 'Elena Vargas Torres',
        'email' => 'elena.vargas@universidad.edu',
        'carrera' => 'Psicología',
        'semestre' => 5,
        'fecha_ingreso' => '2022-08-10',
        'telefono' => '+505 8654-3210',
        'direccion' => 'Estelí, Nicaragua',
        'estado' => 'activo'
    ],
    6 => [
        'id' => 6,
        'nombre' => 'Roberto Jiménez Flores',
        'email' => 'roberto.jimenez@universidad.edu',
        'carrera' => 'Analista de Sistemas',
        'semestre' => 7,
        'fecha_ingreso' => '2021-08-25',
        'telefono' => '+505 7432-1098',
        'direccion' => 'Chinandega, Nicaragua',
        'estado' => 'activo'
    ]
];

// Array de materias por carrera

$materias = [
    'Analista de Sistemas' => [
        'Programacion I',
        'Matemática I',
        'Sistema de Datos I',
        'Programacion II'
    ],
    'Administración de Empresas' => [
        'Contabilidad I',
        'Economía I',
        'Administración I',
        'Marketing I'
    ],
    'Medicina' => [
        'Anatomía I',
        'Fisiología I',
        'Bioquímica I',
        'Farmacología I'
    ],
    'Ingeniería Civil' => [
        'Matemáticas I',
        'Física I',
        'Química I',
        'Dibujo Técnico I'
    ],
    'Psicología' => [
        'Psicología General I',
        'Psicología del Desarrollo I',
        'Psicología Social I',
        'Psicología Clínica I'
    ]
];

// Array de notas (estudiante_id => [materia => nota])
$notas = [
    1 => [
        'Programacion I' => 85,
        'Matemática I' => 90,
        'Sistema de Datos I' => 88,
        'Programacion II' => 92
    ],
    2 => [
        'Contabilidad I' => 78,
        'Economía I' => 82,
        'Administración I' => 80,
        'Marketing I' => 75
    ],
    3 => [
        'Anatomía I' => 95,
        'Fisiología I' => 93,
        'Bioquímica I' => 90,
        'Farmacología I' => 88
    ],
    4 => [
        'Matemáticas I' => 80,
        'Física I' => 85,
        'Química I' => 78,
        'Dibujo Técnico I' => 82
    ],
    5 => [
        'Psicología General I' => 88,
        'Psicología del Desarrollo I' => 90,
        'Psicología Social I' => 85,
        'Psicología Clínica I' => 87
    ]
];

// Calcula el promedio de un estudiante

function calcularPromedio($estudiante_id, $notas){
    global $notas;
    if(!isset($notas[$estudiante_id]) || empty($notas[$estudiante_id])){
        return 0;
    }
    $total_notas = array_sum($notas[$estudiante_id]);
    $cantidad_materias = count($notas[$estudiante_id]);

    return round($total_notas / $cantidad_materias, 2);
}

// Estado academico

function obtenerEstadoAcademico($promedio){
    if($promedio >= 90){
        return 'Excelente';
    } elseif($promedio >= 80){
        return 'Bueno';
    } elseif($promedio >= 70){
        return 'Regular';
    } else {
        return 'Necesita Mejorar';
    }
}

function obtenerEstudiantesPorCarrera($carrera){
    global $estudiantes;

    return array_filter($estudiantes, function($estudiante) use ($carrera) {
        return $estudiante['carrera'] === $carrera;
    });
}

// Busca estudiantes por nombre o email

function buscarEstudiantes($termino){
    global $estudiantes;

    $termino = strtolower($termino);
    return array_filter($estudiantes, function($estudiante) use ($termino) {
        return strpos(strtolower($estudiante['nombre']), $termino) !== false || strpos(strtolower($estudiante['email']), $termino) !== false;
    });
}

// Obtener carreras disponibles

function obtenerCarreras(){
    global $estudiantes;

    $carreras = array_unique(array_column($estudiantes, 'carrera'));
    sort($carreras);
    return $carreras;
}

function obtenerEstadisticasGenerales(){
    global $estudiantes, $notas;

    $total_estudiantes = count($estudiantes);
    $estudiantes_con_notas = count($notas);
    $carreras = obtenerCarreras();
    $total_carreras = count($carreras);

    $suma_promedios = 0;
    $contador = 0;

    foreach($estudiantes as $id => $estudiante) {
        $promedio = calcularPromedio($id, $notas);
        if($promedio > 0) {
            $suma_promedios += $promedio;
            $contador++;
        }
    }

    $promedio_general = $contador > 0 ? round($suma_promedios / $contador, 2) : 0;

    return [
        'total_estudiantes' => $total_estudiantes,
        'estudiantes_con_notas' => $estudiantes_con_notas,
        'total_carreras' => $total_carreras,
        'promedio_general' => $promedio_general,
        'carreras' => $carreras
    ];
}

function obtenerRankingEstudiantes(){
    global $estudiantes, $notas;

    $ranking = [];

    foreach ($estudiantes as $id => $estudiante){
        $promedio = calcularPromedio($id, $notas);
        if($promedio > 0) {
            $ranking[] = [
                'estudiante' => $estudiante,
                'promedio' => $promedio,
                'estado' => obtenerEstadoAcademico($promedio)
            ];
        }
    }

    // Ordenar el ranking por promedio
    usort($ranking, function($a, $b) {
        return $b['promedio'] <=> $a['promedio'];
    });

    return $ranking;
}