<script setup lang="ts">
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'

import GilantFooter from '@/components/GilantFooter.vue'
import GilantHeader from '@/components/GilantHeader.vue'
import GilantHero from '@/components/GilantHero.vue'
import ItemCards from '@/components/ItemCards.vue'

import '../../css/gilant-landing.css'

// -----------------------------------------------------------------------------
// TIPOS
// -----------------------------------------------------------------------------

interface Banner {
    title: string
    subtitle: string | null
    description: string | null
    image: string
    image_url: string
    alt_text: string | null
    link_url: string | null
    button_text: string | null
    open_in_new_tab: boolean
}

interface Service {
    id: number
    slug: string
    title: string
    description: string | null
    image: string | null
    imageAlt: string
}

const props = defineProps<{
    banner: Banner | null
    projects: Service[]
    maquila: Service[]
}>()

// -----------------------------------------------------------------------------
// CONFIGURACIÓN SEO
// -----------------------------------------------------------------------------

const siteUrl = 'https://industriashusky.com.mx'

const seoTitle = 'Servicios de madera | Industrias Husky'

const seoDescription =
    'Conoce los servicios de Industrias Husky: proyectos de madera y servicios de maquila para construcción, infraestructura, industria y proyectos especiales.'

const canonicalUrl = `${siteUrl}/servicios`

/**
 * Convierte rutas relativas en URLs absolutas.
 * Conserva las URLs que ya son absolutas.
 */
const absoluteUrl = (
    url: string | null | undefined,
): string | undefined => {
    if (!url) return undefined

    try {
        return new URL(url, `${siteUrl}/`).toString()
    } catch {
        return undefined
    }
}

// -----------------------------------------------------------------------------
// IMAGEN PARA COMPARTIR LA PÁGINA
// -----------------------------------------------------------------------------

const ogImage = computed(
    () =>
        absoluteUrl(props.banner?.image_url) ||
        `${siteUrl}/images/og-default.jpg`,
)

// -----------------------------------------------------------------------------
// TEXTO ALTERNATIVO PARA LA IMAGEN SOCIAL
// -----------------------------------------------------------------------------

const ogImageAlt = computed(
    () =>
        props.banner?.alt_text ||
        props.banner?.title ||
        'Servicios de Industrias Husky',
)

// -----------------------------------------------------------------------------
// COLECCIONES PARA SEO
// -----------------------------------------------------------------------------

const allServices = computed(() => [
    ...props.projects,
    ...props.maquila,
])

// -----------------------------------------------------------------------------
// DATOS ESTRUCTURADOS
// -----------------------------------------------------------------------------

const collectionSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    name: seoTitle,
    description: seoDescription,
    url: canonicalUrl,

    isPartOf: {
        '@type': 'WebSite',
        name: 'Industrias Husky',
        url: `${siteUrl}/`,
    },

    mainEntity: {
        '@type': 'ItemList',
        numberOfItems: allServices.value.length,

        itemListElement: allServices.value.map((service, index) => ({
            '@type': 'ListItem',
            position: index + 1,

            item: {
                '@type': 'Service',
                name: service.title,

                ...(service.description
                    ? { description: service.description }
                    : {}),

                ...(absoluteUrl(service.image)
                    ? { image: absoluteUrl(service.image) }
                    : {}),

                url: `${canonicalUrl}/${encodeURIComponent(service.slug)}`,

                provider: {
                    '@type': 'Organization',
                    name: 'Industrias Husky',
                    url: `${siteUrl}/`,
                },
            },
        })),
    },
}))

const collectionSchemaJson = computed(() =>
    JSON.stringify(collectionSchema.value),
)

// -----------------------------------------------------------------------------
// PROYECTOS
// -----------------------------------------------------------------------------

const projects = computed(() =>
    props.projects.map((service, index) => ({
        id: service.id,
        slug: service.slug,
        number: String(index + 1).padStart(2, '0'),
        title: service.title,
        description: service.description || '',
        image: service.image,
        imageAlt: service.imageAlt || service.title,
        href: `/servicios/${service.slug}`,
    })),
)

// -----------------------------------------------------------------------------
// MAQUILA
// -----------------------------------------------------------------------------

const maquila = computed(() =>
    props.maquila.map((service, index) => ({
        id: service.id,
        slug: service.slug,
        number: String(index + 1).padStart(2, '0'),
        title: service.title,
        description: service.description || '',
        image: service.image,
        imageAlt: service.imageAlt || service.title,
        href: `/servicios/${service.slug}`,
    })),
)
</script>

<template>
    <Head>
        <title>{{ seoTitle }}</title>

        <!-- SEO -->
        <meta
            head-key="description"
            name="description"
            :content="seoDescription"
        />

        <link
            head-key="canonical"
            rel="canonical"
            :href="canonicalUrl"
        />

        <meta
            head-key="theme-color"
            name="theme-color"
            content="#243b30"
        />

        <meta
            head-key="author"
            name="author"
            content="Industrias Husky"
        />

        <meta
            head-key="language"
            name="language"
            content="es-MX"
        />

        <!-- OPEN GRAPH -->
        <meta
            head-key="og:type"
            property="og:type"
            content="website"
        />

        <meta
            head-key="og:site_name"
            property="og:site_name"
            content="Industrias Husky"
        />

        <meta
            head-key="og:title"
            property="og:title"
            :content="seoTitle"
        />

        <meta
            head-key="og:description"
            property="og:description"
            :content="seoDescription"
        />

        <meta
            head-key="og:url"
            property="og:url"
            :content="canonicalUrl"
        />

        <meta
            head-key="og:image"
            property="og:image"
            :content="ogImage"
        />

        <meta
            head-key="og:image:alt"
            property="og:image:alt"
            :content="ogImageAlt"
        />

        <meta
            head-key="og:locale"
            property="og:locale"
            content="es_MX"
        />

        <!-- TWITTER -->
        <meta
            head-key="twitter:card"
            name="twitter:card"
            content="summary_large_image"
        />

        <meta
            head-key="twitter:title"
            name="twitter:title"
            :content="seoTitle"
        />

        <meta
            head-key="twitter:description"
            name="twitter:description"
            :content="seoDescription"
        />

        <meta
            head-key="twitter:image"
            name="twitter:image"
            :content="ogImage"
        />

        <meta
            head-key="twitter:image:alt"
            name="twitter:image:alt"
            :content="ogImageAlt"
        />

        <!-- DATOS ESTRUCTURADOS -->
        <component
            :is="'script'"
            type="application/ld+json"
            head-key="services-collection-schema"
            v-html="collectionSchemaJson"
        />
    </Head>

    <div class="services-page">
        <GilantHeader />

        <main>
            <!-- HERO -->
            <GilantHero
                :banner="props.banner"
                scroll-target="#catalogo-servicios"
                min-height="60vh"
            />

            <!-- INTRODUCCIÓN GENERAL -->
            <section
                id="catalogo-servicios"
                class="services-intro"
            >
                <div class="services-container">
                    <div class="services-intro-grid">
                        <div class="services-intro-heading">
                            <span class="services-eyebrow">
                                LO QUE HACEMOS
                            </span>

                            <h1 class="services-title">
                                Soluciones en madera
                                <span>para cada proyecto.</span>
                            </h1>
                        </div>

                        <div class="services-intro-content">
                            <p>
                                En Industrias Husky desarrollamos soluciones
                                en madera para diferentes necesidades.
                                Conoce nuestros proyectos y servicios de
                                maquila, respaldados por nuestra experiencia
                                y compromiso con la calidad.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN DE PROYECTOS -->
            <ItemCards
                id="proyectos"
                variant="grid"
                :items="projects"
                :heading-level="2"
                eyebrow="NUESTRO TRABAJO"
                title="Proyectos"
                :compact="true"
                empty-text="Estamos preparando nuestra sección de proyectos."
            />

            <!-- SECCIÓN DE MAQUILA -->
            <ItemCards
                id="maquila"
                variant="grid"
                :items="maquila"
                :heading-level="2"
                eyebrow="SOLUCIONES A TU MEDIDA"
                title="Maquila"
                :compact="true"
                empty-text="Estamos preparando nuestra sección de maquila."
            />
        </main>

        <GilantFooter />
    </div>
</template>

<style scoped>
/* =========================================================
   SERVICIOS — INDUSTRIAS HUSKY
   Identidad: bosque, olivo, arena y madera
========================================================= */

.services-page {
    --husky-forest: #243b30;
    --husky-forest-dark: #172b21;
    --husky-olive: #78875b;
    --husky-olive-light: #a5b18b;
    --husky-sand: #e9e1d2;
    --husky-cream: #f7f5ef;
    --husky-paper: #fffefa;
    --husky-wood: #b58b5b;
    --husky-ink: #29372e;
    --husky-muted: #727c72;

    width: 100%;
    min-width: 0;
    overflow: clip;
    color: var(--husky-ink);
    background: var(--husky-paper);
}

.services-page main {
    width: 100%;
    min-width: 0;
}

.services-container {
    width: min(88%, 1440px);
    margin-inline: auto;
}

/* =========================================================
   INTRODUCCIÓN
========================================================= */

.services-intro {
    position: relative;
    width: 100%;
    padding: clamp(85px, 10vw, 145px) 0;
    overflow: hidden;
    background: var(--husky-paper);
}

.services-intro-grid {
    display: grid;
    grid-template-columns: 0.9fr 1.1fr;
    align-items: center;
    gap: clamp(55px, 9vw, 150px);
}

.services-intro-heading {
    position: relative;
    max-width: 600px;
}

.services-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    color: var(--husky-olive);
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.2em;
    line-height: 1.5;
    text-transform: uppercase;
}

.services-eyebrow::before {
    width: 28px;
    height: 1px;
    background: var(--husky-wood);
    content: '';
}

.services-title {
    margin: 22px 0 0;
    color: var(--husky-forest);
    font-family: 'Manrope', sans-serif;
    font-size: clamp(2.8rem, 5.5vw, 5.3rem);
    font-weight: 600;
    letter-spacing: -0.075em;
    line-height: 1.02;
}

.services-title span {
    display: block;
    color: var(--husky-olive);
}

.services-intro-heading::after {
    display: block;
    width: 66px;
    height: 3px;
    margin-top: 34px;
    background: var(--husky-wood);
    content: '';
}

.services-intro-content {
    position: relative;
    padding: 12px 0 12px 34px;
    border-left: 1px solid #dce1d7;
}

.services-intro-content p {
    margin: 0;
    color: #626f64;
    font-size: clamp(1.02rem, 1.3vw, 1.16rem);
    line-height: 2;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 850px) {
    .services-container {
        width: 92%;
    }

    .services-intro-grid {
        grid-template-columns: 1fr 1fr;
        gap: 45px;
    }
}

@media (max-width: 650px) {
    .services-container {
        width: 90%;
    }

    .services-intro {
        padding: 78px 0;
    }

    .services-intro-grid {
        grid-template-columns: 1fr;
        gap: 35px;
    }

    .services-intro-content {
        padding: 0 0 0 22px;
    }

    .services-intro-content p {
        font-size: 1rem;
    }
}

@media (max-width: 420px) {
    .services-title {
        font-size: 2.65rem;
    }
}
</style>