<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import {
    ArrowRight,
    Award,
    CheckCircle2,
    Compass,
    Eye,
    Factory,
    Gem,
    Handshake,
    HeartHandshake,
    Leaf,
    Lightbulb,
    ShieldCheck,
    Sprout,
    Star,
    Target,
    TreeDeciduous,
    Users,
    type LucideIcon,
} from 'lucide-vue-next'

import GilantFooter from '@/components/GilantFooter.vue'
import GilantHeader from '@/components/GilantHeader.vue'
import GilantHero from '@/components/GilantHero.vue'
import ItemCards from '@/components/ItemCards.vue'

import '../../css/gilant-landing.css'

// -----------------------------------------------------------------------------
// TIPOS
// -----------------------------------------------------------------------------

interface Banner {
    id: number
    title?: string | null
    subtitle?: string | null
    description?: string | null
    image_url?: string | null
    video_url?: string | null
    button_text?: string | null
    button_url?: string | null
}

interface AboutValue {
    id: number
    name: string
    description: string
    icon?: string | null
    sort_order?: number
}

interface AboutGalleryImage {
    id: number
    file: string
    url: string
    alt: string | null
    is_primary: boolean
    sort_order: number
}

interface About {
    id: number
    who_we_are?: string | null
    history?: string | null
    mission?: string | null
    vision?: string | null
    objectives?: string | null
    values?: AboutValue[]
    gallery_images?: AboutGalleryImage[]
}

const props = defineProps<{
    banner: Banner | null
    about: About | null
}>()

// -----------------------------------------------------------------------------
// SEO
// -----------------------------------------------------------------------------

const seoTitle =
    'Nosotros | Historia y experiencia en madera | Industrias Husky'

const seoDescription =
    'Conoce la historia de Industrias Husky, nuestra experiencia en el tratamiento de madera, misión, visión y valores que respaldan nuestro compromiso con la calidad.'

const canonicalUrl =
    'https://industriashusky.com.mx/nosotros'

const ogImage =
    'https://industriashusky.com.mx/images/og-default.jpg'

const aboutPageSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'AboutPage',
    name: seoTitle,
    description: seoDescription,
    url: canonicalUrl,
    inLanguage: 'es-MX',
    isPartOf: {
        '@type': 'WebSite',
        name: 'Industrias Husky',
        url: 'https://industriashusky.com.mx/',
    },
    about: {
        '@type': 'Organization',
        name: 'Industrias Husky',
        url: 'https://industriashusky.com.mx/',
    },
}))

const aboutPageSchemaJson = computed(() =>
    JSON.stringify(aboutPageSchema.value),
)

// -----------------------------------------------------------------------------
// ICONOS DE VALORES
// -----------------------------------------------------------------------------

const iconMap: Record<string, LucideIcon> = {
    arrowright: ArrowRight,
    award: Award,
    checkcircle2: CheckCircle2,
    compass: Compass,
    eye: Eye,
    factory: Factory,
    gem: Gem,
    handshake: Handshake,
    hearthandshake: HeartHandshake,
    leaf: Leaf,
    lightbulb: Lightbulb,
    shieldcheck: ShieldCheck,
    sprout: Sprout,
    star: Star,
    target: Target,
    treedeciduous: TreeDeciduous,
    tree: TreeDeciduous,
    users: Users,
}

// -----------------------------------------------------------------------------
// VALORES
// -----------------------------------------------------------------------------

const values = computed(() => props.about?.values ?? [])

// -----------------------------------------------------------------------------
// GALERÍA DE HISTORIA
// -----------------------------------------------------------------------------

const historyGallery = computed(() => {
    const images = (props.about?.gallery_images ?? [])
        .slice()
        .sort((a, b) => a.sort_order - b.sort_order)

    if (!images.length) {
        return [
            {
                id: 'history',
                title: 'Nuestra historia',
                description: props.about?.history ?? '',
                image: null,
                alt: 'Historia de Industrias Husky',
            },
        ]
    }

    return images.map((image, index) => ({
        id: image.id,
        title: index === 0 ? 'Nuestra historia' : '',
        description: index === 0 ? (props.about?.history ?? '') : '',
        image: image.url,
        alt: image.alt?.trim() || 'Imagen de la historia de Industrias Husky',
    }))
})

// -----------------------------------------------------------------------------
// MISIÓN, VISIÓN Y OBJETIVOS
// -----------------------------------------------------------------------------

const sections = computed(() => [
    {
        id: 'mission',
        label: 'Nuestra misión',
        title: 'Lo que nos mueve',
        description: props.about?.mission,
        icon: Target,
    },
    {
        id: 'vision',
        label: 'Nuestra visión',
        title: 'Hacia dónde vamos',
        description: props.about?.vision,
        icon: Eye,
    },
    {
        id: 'objectives',
        label: 'Nuestros objetivos',
        title: 'Lo que buscamos',
        description: props.about?.objectives,
        icon: Compass,
    },
].filter(section => section.description?.trim()))

// -----------------------------------------------------------------------------
// OBTENER ICONO DE VALOR
// -----------------------------------------------------------------------------

function getValueIcon(icon?: string | null): LucideIcon {
    if (!icon) return Star

    const normalizedIcon = icon
        .replace(/[-_\s]/g, '')
        .toLowerCase()

    return iconMap[normalizedIcon] || Star
}
</script>

<template>
    <Head :title="seoTitle">
        <!-- SEO PRINCIPAL -->
        <meta
            name="description"
            :content="seoDescription"
        />

        <meta
            name="author"
            content="Industrias Husky"
        />

        <meta
            name="robots"
            content="index, follow"
        />

        <meta
            name="language"
            content="es-MX"
        />

        <meta
            name="theme-color"
            content="#243b30"
        />

        <!-- URL CANÓNICA -->
        <link
            rel="canonical"
            :href="canonicalUrl"
        />

        <!-- OPEN GRAPH -->
        <meta
            property="og:type"
            content="website"
        />

        <meta
            property="og:site_name"
            content="Industrias Husky"
        />

        <meta
            property="og:title"
            :content="seoTitle"
        />

        <meta
            property="og:description"
            :content="seoDescription"
        />

        <meta
            property="og:url"
            :content="canonicalUrl"
        />

        <meta
            property="og:image"
            :content="ogImage"
        />

        <meta
            property="og:image:alt"
            content="Industrias Husky, soluciones en madera"
        />

        <meta
            property="og:locale"
            content="es_MX"
        />

        <!-- TWITTER CARD -->
        <meta
            name="twitter:card"
            content="summary_large_image"
        />

        <meta
            name="twitter:title"
            :content="seoTitle"
        />

        <meta
            name="twitter:description"
            :content="seoDescription"
        />

        <meta
            name="twitter:image"
            :content="ogImage"
        />

        <!-- DATOS ESTRUCTURADOS -->
        <component
            :is="'script'"
            type="application/ld+json"
            v-html="aboutPageSchemaJson"
        />
    </Head>

    <div class="about-page">
        <GilantHeader />

        <main>
            <!-- HERO -->
            <GilantHero
                :banner="props.banner"
                scroll-target="#nosotros"
                min-height="60vh"
            />

            <!-- INTRODUCCIÓN -->
            <section
                id="nosotros"
                class="about-section about-intro"
            >
                <div class="about-container">
                    <div class="intro-grid">
                        <div class="intro-heading">
                            <span class="about-eyebrow">
                                CONÓCENOS
                            </span>

                            <h1 class="about-title">
                                Somos <span>Industrias Husky</span>
                            </h1>
                        </div>

                        <div class="intro-content">
                            <p
                                v-if="about?.who_we_are"
                                class="about-description"
                            >
                                {{ about.who_we_are }}
                            </p>

                            <p
                                v-else
                                class="about-description"
                            >
                                En Industrias Husky trabajamos para ofrecer
                                soluciones en madera que respondan a las
                                necesidades de cada proyecto, combinando
                                calidad, experiencia y compromiso.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- HISTORIA -->
            <ItemCards
                v-if="about?.history"
                variant="featured"
                eyebrow="NUESTRA TRAYECTORIA"
                title="Una historia construida con compromiso"
                :items="historyGallery"
                background="#F7F4EC"
                padding-top="90px"
                padding-bottom="90px"
                gallery-position="right"
            />

            <!-- FILOSOFÍA -->
            <section
                v-if="sections.length"
                class="about-section about-purpose"
            >
                <div class="about-container">
                    <div class="section-heading">
                        <span class="about-eyebrow">
                            NUESTRA FILOSOFÍA
                        </span>

                        <h2 class="about-heading">
                            Lo que nos define
                        </h2>

                        <p class="section-description">
                            Nuestra forma de trabajar se basa en principios
                            que orientan cada decisión y cada proyecto.
                        </p>
                    </div>

                    <div class="purpose-grid">
                        <article
                            v-for="(section, index) in sections"
                            :key="section.id"
                            class="purpose-card"
                            :class="`purpose-card-${index + 1}`"
                        >
                            <div class="purpose-card-top">
                                <span class="purpose-number">
                                    0{{ index + 1 }}
                                </span>

                                <component
                                    :is="section.icon"
                                    class="purpose-icon"
                                />
                            </div>

                            <span class="purpose-label">
                                {{ section.label }}
                            </span>

                            <h3>{{ section.title }}</h3>

                            <p>{{ section.description }}</p>

                            <div class="purpose-card-decoration"></div>
                        </article>
                    </div>
                </div>
            </section>

            <!-- VALORES -->
            <section
                v-if="values.length"
                class="about-section about-values"
            >
                <div class="about-container">
                    <div class="section-heading values-heading">
                        <span class="about-eyebrow">
                            NUESTROS VALORES
                        </span>

                        <h2 class="about-heading">
                            Principios que nos representan
                        </h2>

                        <p class="section-description">
                            Son la base de nuestras relaciones, nuestro
                            trabajo y la manera en que desarrollamos
                            cada proyecto.
                        </p>
                    </div>

                    <div class="values-grid">
                        <article
                            v-for="(value, index) in values"
                            :key="value.id"
                            class="value-card"
                        >
                            <div class="value-card-header">
                                <div class="value-icon-wrapper">
                                    <component
                                        :is="getValueIcon(value.icon)"
                                        class="value-icon"
                                    />
                                </div>

                                <span class="value-number">
                                    {{ String(index + 1).padStart(2, '0') }}
                                </span>
                            </div>

                            <h3>{{ value.name }}</h3>

                            <p>{{ value.description }}</p>

                            <div class="value-card-line"></div>
                        </article>
                    </div>
                </div>
            </section>
        </main>

        <GilantFooter />
    </div>
</template>

<style scoped>
/* =========================================================
   NOSOTROS — INDUSTRIAS HUSKY
   Identidad: bosque, olivo, arena y madera
========================================================= */

.about-page {
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

.about-container {
    width: min(88%, 1440px);
    margin-inline: auto;
}

.about-section {
    position: relative;
    width: 100%;
    padding: clamp(76px, 8vw, 120px) 0;
}

/* ELEMENTOS GENERALES */

.about-eyebrow {
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

.about-eyebrow::before {
    width: 28px;
    height: 1px;
    background: var(--husky-wood);
    content: '';
}

.about-title {
    margin: 22px 0 0;
    color: var(--husky-forest);
    font-family: 'Manrope', sans-serif;
    font-size: clamp(2.8rem, 5.5vw, 5.3rem);
    font-weight: 600;
    letter-spacing: -0.075em;
    line-height: 1.02;
}

.about-title span {
    display: block;
    color: var(--husky-olive);
}

.about-heading {
    margin: 18px 0 24px;
    color: var(--husky-forest);
    font-family: 'Manrope', sans-serif;
    font-size: clamp(2.1rem, 3.8vw, 3.6rem);
    font-weight: 600;
    letter-spacing: -0.06em;
    line-height: 1.12;
}

.about-description {
    color: var(--husky-muted);
    font-size: 1rem;
    line-height: 1.95;
    white-space: pre-line;
}

.section-description {
    max-width: 570px;
    margin: 0 auto;
    color: var(--husky-muted);
    font-size: 0.98rem;
    line-height: 1.9;
}

/* INTRODUCCIÓN */

.about-intro {
    padding-top: clamp(85px, 10vw, 145px);
    padding-bottom: clamp(85px, 10vw, 145px);
    background: var(--husky-paper);
}

.intro-grid {
    display: grid;
    grid-template-columns: 0.9fr 1.1fr;
    align-items: center;
    gap: clamp(55px, 9vw, 150px);
}

.intro-heading {
    position: relative;
    max-width: 570px;
}

.intro-heading::after {
    display: block;
    width: 66px;
    height: 3px;
    margin-top: 34px;
    background: var(--husky-wood);
    content: '';
}

.intro-content {
    position: relative;
    padding: 12px 0 12px 34px;
    border-left: 1px solid #dce1d7;
}

.intro-content .about-description {
    margin: 0;
    color: #626f64;
    font-size: clamp(1.02rem, 1.3vw, 1.16rem);
    line-height: 2;
}

/* FILOSOFÍA */

.about-purpose {
    background: var(--husky-cream);
}

.section-heading {
    max-width: 700px;
    margin: 0 auto 62px;
    text-align: center;
}

.section-heading .about-eyebrow {
    justify-content: center;
}

.section-heading .about-heading {
    margin-bottom: 18px;
}

.purpose-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
    align-items: stretch;
}

.purpose-card {
    position: relative;
    display: flex;
    min-height: 360px;
    overflow: hidden;
    flex-direction: column;
    align-items: flex-start;
    padding: clamp(27px, 3vw, 40px);
    border: 1px solid #e2e5db;
    border-radius: 3px;
    background: var(--husky-paper);
    transition: transform 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease;
}

.purpose-card:hover {
    transform: translateY(-7px);
    border-color: var(--husky-olive-light);
    box-shadow: 0 20px 45px rgb(36 59 48 / 8%);
}

.purpose-card-top {
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 52px;
}

.purpose-number {
    color: #aab3a4;
    font-family: 'Manrope', sans-serif;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.12em;
}

.purpose-icon {
    width: 34px;
    height: 34px;
    color: var(--husky-olive);
    stroke-width: 1.4;
}

.purpose-label {
    margin-bottom: 13px;
    color: var(--husky-olive);
    font-size: 0.69rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.purpose-card h3 {
    position: relative;
    z-index: 1;
    margin: 0 0 17px;
    color: var(--husky-forest);
    font-family: 'Manrope', sans-serif;
    font-size: 1.5rem;
    font-weight: 650;
    letter-spacing: -0.045em;
}

.purpose-card p {
    position: relative;
    z-index: 1;
    margin: 0;
    color: var(--husky-muted);
    font-size: 0.92rem;
    line-height: 1.9;
    white-space: pre-line;
}

.purpose-card-decoration {
    position: absolute;
    right: -48px;
    bottom: -65px;
    width: 170px;
    height: 170px;
    border: 1px solid #e8ebe3;
    border-radius: 50%;
    transition: transform 0.5s ease;
}

.purpose-card:hover .purpose-card-decoration {
    transform: scale(1.18);
}

/* VALORES */

.about-values {
    overflow: hidden;
    background: var(--husky-paper);
}

.about-values::before {
    position: absolute;
    top: 0;
    right: 0;
    width: 35%;
    height: 100%;
    background: linear-gradient(135deg, transparent, rgb(233 225 210 / 32%));
    content: '';
    pointer-events: none;
}

.values-heading {
    position: relative;
    z-index: 1;
    margin-bottom: 58px;
}

.values-grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 17px;
    align-items: stretch;
}

.value-card {
    position: relative;
    display: flex;
    min-height: 270px;
    overflow: hidden;
    flex-direction: column;
    padding: 28px;
    border: 1px solid #e5e8df;
    border-radius: 3px;
    background: #fff;
    transition: transform 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease;
}

.value-card:hover {
    transform: translateY(-6px);
    border-color: #b8c4a9;
    box-shadow: 0 18px 38px rgb(36 59 48 / 8%);
}

.value-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 35px;
}

.value-icon-wrapper {
    display: grid;
    width: 58px;
    height: 58px;
    place-items: center;
    border: 1px solid #e0e7d9;
    border-radius: 50%;
    background: #f0f3eb;
    transition: color 0.3s ease, background 0.3s ease, border-color 0.3s ease;
}

.value-card:hover .value-icon-wrapper {
    border-color: var(--husky-forest);
    color: white;
    background: var(--husky-forest);
}

.value-icon {
    width: 26px;
    height: 26px;
    color: var(--husky-forest);
    stroke-width: 1.6;
}

.value-card:hover .value-icon {
    color: white;
}

.value-number {
    color: #aab3a4;
    font-size: 0.76rem;
    font-weight: 800;
    letter-spacing: 0.12em;
}

.value-card h3 {
    margin: 0 0 13px;
    color: var(--husky-forest);
    font-family: 'Manrope', sans-serif;
    font-size: 1.2rem;
    font-weight: 700;
    letter-spacing: -0.035em;
}

.value-card p {
    margin: 0;
    color: var(--husky-muted);
    font-size: 0.88rem;
    line-height: 1.85;
    white-space: pre-line;
}

.value-card-line {
    width: 38px;
    height: 2px;
    margin-top: auto;
    padding-top: 23px;
    border-bottom: 2px solid var(--husky-wood);
    transition: width 0.3s ease;
}

.value-card:hover .value-card-line {
    width: 65px;
}

/* RESPONSIVE */

@media (max-width: 1100px) {
    .values-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .purpose-card {
        min-height: 340px;
    }
}

@media (max-width: 850px) {
    .about-container {
        width: 92%;
    }

    .intro-grid {
        grid-template-columns: 1fr 1fr;
        gap: 45px;
    }

    .purpose-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .values-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 650px) {
    .about-container {
        width: 90%;
    }

    .about-section {
        padding: 72px 0;
    }

    .about-intro {
        padding-top: 78px;
        padding-bottom: 78px;
    }

    .intro-grid {
        grid-template-columns: 1fr;
        gap: 35px;
    }

    .intro-content {
        padding: 0 0 0 22px;
    }

    .intro-content .about-description {
        font-size: 1rem;
    }

    .section-heading {
        margin-bottom: 40px;
    }

    .purpose-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .purpose-card {
        min-height: 285px;
        padding: 28px;
    }

    .purpose-card-top {
        margin-bottom: 35px;
    }

    .values-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .value-card {
        min-height: 245px;
        padding: 21px;
    }

    .value-card-header {
        margin-bottom: 27px;
    }
}

@media (max-width: 420px) {
    .about-title {
        font-size: 2.65rem;
    }

    .about-heading {
        font-size: 2rem;
    }

    .values-grid {
        grid-template-columns: 1fr;
    }

    .value-card {
        min-height: 220px;
    }
}

/* ACCESIBILIDAD */

@media (prefers-reduced-motion: reduce) {
    .purpose-card,
    .purpose-card-decoration,
    .value-card,
    .value-icon-wrapper,
    .value-card-line {
        transition: none;
    }

    .purpose-card:hover,
    .value-card:hover {
        transform: none;
    }
}
</style>