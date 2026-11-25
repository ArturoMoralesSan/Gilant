<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Textos extraídos de "Portafolio_Grupo_Gilant_Actualizado_Clientes_Calidad.pdf".
 * Solo información textual (sin imágenes).
 */
class GilantPortfolioSeeder extends Seeder
{
    public function run(): void
    {
        // Nosotros
        $about = About::updateOrCreate(['id' => 1], [
            'who_we_are' => "En Grupo Gilant del Noroeste, S.A. de C.V. somos especialistas en la construcción con madera estructural y en proyectos de obra exterior. Combinamos experiencia, conocimiento técnico y un profundo respeto por la naturaleza para crear estructuras sólidas, funcionales y de gran belleza.\n\nTrabajamos con maderas de alta calidad, diseño personalizado y un equipo comprometido con la excelencia en cada proyecto, desde la planeación hasta la instalación.",
            'history' => 'Construimos con madera donde la arquitectura se encuentra con la naturaleza.',
        ]);

        $values = [
            'Experiencia en madera',
            'Fabricación y montaje',
            'Calidad y durabilidad',
            'Compromiso con el medio',
        ];
        foreach ($values as $i => $name) {
            $about->values()->updateOrCreate(['name' => $name], ['sort_order' => $i]);
        }

        // Líneas de trabajo (servicios y proyectos)
        $lines = [
            [
                'name' => 'Estructuras de Madera & Mass Timber',
                'short' => 'Grandes claros, vigas, columnas y sistemas estructurales de madera.',
                'description' => 'Grandes claros, vigas, columnas y sistemas estructurales de madera.',
                'applications' => null,
            ],
            [
                'name' => 'Decks & Andadores',
                'short' => 'Terrazas, pasarelas y soluciones de circulación exterior en madera de alta resistencia.',
                'description' => 'Terrazas, pasarelas y soluciones de circulación exterior en madera de alta resistencia.',
                'applications' => null,
            ],
            [
                'name' => 'Estructuras Elevadas & en la Naturaleza',
                'short' => 'Puentes, pasarelas y estructuras en entornos naturales, selvas y manglares.',
                'description' => 'Puentes, pasarelas y estructuras en entornos naturales, selvas y manglares. Incluye puentes de madera para carritos de golf y vehículos.',
                'applications' => null,
            ],
            [
                'name' => 'Cabañas & Casas en el Árbol',
                'short' => 'Espacios únicos que se integran con el entorno.',
                'description' => 'Espacios únicos que se integran con el entorno.',
                'applications' => null,
            ],
            [
                'name' => 'Muelles & Marinas',
                'short' => 'Muelles flotantes y fijos, plataformas, andadores y estructuras marítimas.',
                'description' => 'Muelles flotantes y fijos, plataformas, andadores y estructuras marítimas.',
                'applications' => null,
            ],
            [
                'name' => 'Pérgolas & Arquitectura Exterior',
                'short' => 'Espacios de sombra, confort y diseño natural.',
                'description' => 'Diseñamos y construimos espacios exteriores que integran la madera con el entorno, creando áreas de sombra, descanso y convivencia.',
                'applications' => 'Pérgolas · Terrazas · Decks · Áreas de descanso · Hoteles · Resorts · Residencias · Proyectos turísticos',
            ],
            [
                'name' => 'Muros de Contención',
                'short' => 'Soluciones funcionales y duraderas para todo tipo de terreno.',
                'description' => 'Diseñamos y construimos soluciones en madera para contener, delimitar y proteger espacios exteriores, integrando funcionalidad, resistencia y una estética natural.',
                'applications' => 'Muros y contenciones · Bordos y lagos · Paisajismo · Desarrollos turísticos · Campos de golf · Obras exteriores',
            ],
        ];

        foreach ($lines as $i => $line) {
            $description = $line['description']
                .($line['applications'] ? "\n\nAplicaciones: ".$line['applications'] : '');

            Service::updateOrCreate(['slug' => Str::slug($line['name'])], [
                'name' => $line['name'],
                'category' => 'proyectos',
                'short_description' => $line['short'],
                'description' => $description,
                'is_active' => true,
                'sort_order' => $i,
            ]);

            Project::updateOrCreate(['slug' => Str::slug($line['name'])], [
                'name' => $line['name'],
                'category' => $line['name'],
                'description' => $description,
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }

        // Clientes: el logotipo es opcional; sin logo se muestra el nombre.
        $clients = [
            'Grupo Vidanta',
            'Vidanta Riviera Maya',
            'Vidanta Acapulco',
            'Vidanta Riviera Nayarit',
            'Vidanta Los Cabos',
            'RLH Properties · Mandarina',
            'RLH Properties · Mayakoba',
            'Siari · A Ritz-Carlton Reserve',
            'Médica Ledezma',
            'Costa Palmas · Four Seasons · Aman',
            'FONATUR',
            'Rosewood Mandarina',
            'One&Only Mandarina',
            'Hotel Hacienda Los Cabos',
            'Grand Velas Riviera Maya',
            'Banyan Tree Mayakoba',
        ];
        foreach ($clients as $name) {
            Client::firstOrCreate(['name' => $name], ['logo' => null]);
        }

        // Contacto
        Contact::updateOrCreate(['id' => 1], [
            'email' => 'gcrosas@gilant.com.mx',
            'phone_primary' => '618 835 7190 (89)',
            'whatsapp' => '618 189 2080',
        ]);
    }
}
