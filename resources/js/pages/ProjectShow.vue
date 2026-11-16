
<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import {
    ArrowUpRight,
    ChevronLeft,
    ChevronRight,
    X,
    Maximize2,
    TreeDeciduous,
} from 'lucide-vue-next'

import GilantHeader from '@/components/GilantHeader.vue'
import GilantFooter from '@/components/GilantFooter.vue'

import '../../css/gilant-landing.css'

// -----------------------------------------------------------------------------
// TIPOS
// -----------------------------------------------------------------------------

interface GalleryImage {
    id: number
    url: string
    alt: string
    is_primary: boolean
}

interface Project {
    id: number
    name: string
    slug: string
    description: string | null
    short_description?: string | null
    gallery: GalleryImage[]
    main_image: string | null
}

const props = defineProps<{
    project: Project
}>()

// -----------------------------------------------------------------------------
// SEO
// -----------------------------------------------------------------------------

const siteUrl = (
    import.meta.env.VITE_APP_URL || 'https://grupogilant.com'
).replace(/\/$/, '')

function absoluteUrl(url: string | null | undefined): string | undefined {
    if (!url) return undefined

    try {
        return new URL(url, `${siteUrl}/`).toString()
    } catch {
        return undefined
    }
}

const canonicalUrl = computed(
    () => `${siteUrl}/proyectos/${encodeURIComponent(props.project.slug)}`
)

const seoTitle = computed(
    () => `${props.project.name} | Grupo Gilant`
)

const seoDescription = computed(() => {
    const description =
        props.project.short_description ||
        props.project.description ||
        `Conoce ${props.project.name}, un proyecto de construcción con madera estructural y obra exterior de Grupo Gilant.`

    const cleanDescription = description.replace(/\s+/g, ' ').trim()

    return cleanDescription.length > 160
        ? `${cleanDescription.slice(0, 157).trimEnd()}...`
        : cleanDescription
})

// -----------------------------------------------------------------------------
// GALERÍA
// -----------------------------------------------------------------------------

const selectedImage = ref<number | null>(null)
const showGallery = ref(false)

const gallery = computed(() => props.project.gallery || [])
const galleryCount = computed(() => gallery.value.length)

const currentImage = computed<GalleryImage | null>(() => {
    if (selectedImage.value === null) return null

    return gallery.value.find(
        image => image.id === selectedImage.value
    ) || null
})

const galleryIndex = computed(() => {
    if (!currentImage.value) return -1

    return gallery.value.findIndex(
        image => image.id === currentImage.value?.id
    )
})

const hasMultipleImages = computed(() => galleryCount.value > 1)

const projectImage = computed(
    () => currentImage.value?.url || props.project.main_image || ''
)

const absoluteProjectImage = computed(
    () =>
        absoluteUrl(projectImage.value) ||
        `${siteUrl}/images/og-default.jpg`
)

const projectSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'CreativeWork',
    '@id': `${canonicalUrl.value}#project`,
    name: props.project.name,
    description: seoDescription.value,
    image: absoluteProjectImage.value,
    url: canonicalUrl.value,
    creator: {
        '@type': 'Organization',
        name: 'Grupo Gilant',
        url: `${siteUrl}/`,
    },
}))

const projectSchemaJson = computed(
    () => JSON.stringify(projectSchema.value)
)

function selectImage(image: GalleryImage) {
    selectedImage.value = image.id
}

function openGallery() {
    if (!galleryCount.value) return

    showGallery.value = true
}

function closeGallery() {
    showGallery.value = false
}

function changeImage(direction: number) {
    if (!galleryCount.value) return

    const currentIndex = galleryIndex.value >= 0
        ? galleryIndex.value
        : 0

    const nextIndex =
        (currentIndex + direction + galleryCount.value) %
        galleryCount.value

    selectedImage.value = gallery.value[nextIndex].id
}

// -----------------------------------------------------------------------------
// SWIPE TÁCTIL
// -----------------------------------------------------------------------------

interface PointerPosition {
    x: number
    y: number
    pointerId: number
}

const mainPointerStart = ref<PointerPosition | null>(null)
const lightboxPointerStart = ref<PointerPosition | null>(null)

const suppressClick = ref(false)

let clickResetTimer: ReturnType<typeof setTimeout> | null = null

const SWIPE_THRESHOLD = 50

function handlePointerStart(
    event: PointerEvent,
    area: 'main' | 'lightbox',
) {
    if (event.pointerType === 'mouse' && event.button !== 0) return

    const target = event.target

    if (target instanceof Element && target.closest('button')) return

    const position: PointerPosition = {
        x: event.clientX,
        y: event.clientY,
        pointerId: event.pointerId,
    }

    if (area === 'main') {
        mainPointerStart.value = position
    } else {
        lightboxPointerStart.value = position
    }

    const element = event.currentTarget

    if (element instanceof HTMLElement) {
        try {
            element.setPointerCapture(event.pointerId)
        } catch {
            // El navegador puede no permitir capturar el puntero.
        }
    }
}

function handlePointerEnd(
    event: PointerEvent,
    area: 'main' | 'lightbox',
) {
    const start = area === 'main'
        ? mainPointerStart.value
        : lightboxPointerStart.value

    if (!start || start.pointerId !== event.pointerId) return

    if (area === 'main') {
        mainPointerStart.value = null
    } else {
        lightboxPointerStart.value = null
    }

    const deltaX = event.clientX - start.x
    const deltaY = event.clientY - start.y

    if (
        Math.abs(deltaX) < SWIPE_THRESHOLD ||
        Math.abs(deltaX) < Math.abs(deltaY) * 1.2
    ) {
        return
    }

    changeImage(deltaX < 0 ? 1 : -1)

    if (area === 'main') {
        suppressClick.value = true

        if (clickResetTimer) {
            clearTimeout(clickResetTimer)
        }

        clickResetTimer = setTimeout(() => {
            suppressClick.value = false
            clickResetTimer = null
        }, 500)
    }
}

function handlePointerCancel(
    event: PointerEvent,
    area: 'main' | 'lightbox',
) {
    const start = area === 'main'
        ? mainPointerStart.value
        : lightboxPointerStart.value

    if (!start || start.pointerId !== event.pointerId) return

    if (area === 'main') {
        mainPointerStart.value = null
    } else {
        lightboxPointerStart.value = null
    }
}

function handleImageClick() {
    if (suppressClick.value) {
        suppressClick.value = false

        if (clickResetTimer) {
            clearTimeout(clickResetTimer)
            clickResetTimer = null
        }

        return
    }

    openGallery()
}

// -----------------------------------------------------------------------------
// TECLADO
// -----------------------------------------------------------------------------

function handleKeydown(event: KeyboardEvent) {
    if (!showGallery.value) return

    if (event.key === 'Escape') {
        closeGallery()
    }

    if (event.key === 'ArrowRight') {
        changeImage(1)
    }

    if (event.key === 'ArrowLeft') {
        changeImage(-1)
    }
}

// -----------------------------------------------------------------------------
// WATCHERS
// -----------------------------------------------------------------------------

watch(
    () => props.project.gallery,
    images => {
        const availableImages = images || []

        if (!availableImages.length) {
            selectedImage.value = null
            showGallery.value = false
            return
        }

        const currentExists = availableImages.some(
            image => image.id === selectedImage.value
        )

        if (!currentExists) {
            const primary =
                availableImages.find(image => image.is_primary) ||
                availableImages[0]

            selectedImage.value = primary.id
        }
    },
    { immediate: true, deep: true }
)

let previousBodyOverflow = ''

watch(showGallery, isOpen => {
    if (typeof document === 'undefined') return

    if (isOpen) {
        previousBodyOverflow = document.body.style.overflow
        document.body.style.overflow = 'hidden'
    } else {
        document.body.style.overflow = previousBodyOverflow
    }
})

// -----------------------------------------------------------------------------
// CICLO DE VIDA
// -----------------------------------------------------------------------------

onMounted(() => {
    window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown)

    if (clickResetTimer) {
        clearTimeout(clickResetTimer)
    }

    document.body.style.overflow = previousBodyOverflow
})
</script>

<template>
    <Head>
        <title>{{ seoTitle }}</title>

        <meta
            head-key="description"
            name="description"
            :content="seoDescription"
        />

        <meta
            head-key="robots"
            name="robots"
            content="index, follow"
        />

        <link
            head-key="canonical"
            rel="canonical"
            :href="canonicalUrl"
        />

        <meta
            head-key="theme-color"
            name="theme-color"
            content="#f5f3ec"
        />

        <meta
            head-key="author"
            name="author"
            content="Grupo Gilant"
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
            content="article"
        />

        <meta
            head-key="og:site_name"
            property="og:site_name"
            content="Grupo Gilant"
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
            :content="absoluteProjectImage"
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
            :content="absoluteProjectImage"
        />

        <!-- DATOS ESTRUCTURADOS -->
        <component
            :is="'script'"
            type="application/ld+json"
            head-key="project-schema"
            v-html="projectSchemaJson"
        />
    </Head>

    <div class="gilant-site project-detail">
        <GilantHeader />

        <main>
            <!-- DETALLE DEL PROYECTO -->
            <section class="project-detail-section">
                <div class="gilant-container">

                    <div class="project-detail-heading">
                        <span class="project-section-label">
                            <span></span>
                            PORTAFOLIO / PROYECTOS
                        </span>

                        <p>
                            Conoce nuestros proyectos de construcción con
                            madera estructural y obra exterior. Espacios
                            diseñados para integrarse con su entorno y
                            construidos con atención a cada detalle.
                        </p>
                    </div>

                    <div class="project-detail-grid">

                        <!-- GALERÍA -->
                        <div class="project-gallery-column">

                            <div
                                v-if="projectImage"
                                class="project-main-image"
                                @pointerdown="handlePointerStart($event, 'main')"
                                @pointerup="handlePointerEnd($event, 'main')"
                                @pointercancel="handlePointerCancel($event, 'main')"
                                @click="handleImageClick"
                            >
                                <img
                                    :src="projectImage"
                                    :alt="currentImage?.alt || project.name"
                                    draggable="false"
                                />

                                <button
                                    v-if="galleryCount"
                                    type="button"
                                    class="project-expand-button"
                                    aria-label="Ampliar imagen"
                                    @click.stop="openGallery"
                                >
                                    <Maximize2 :size="16" />
                                </button>

                                <template v-if="hasMultipleImages">
                                    <button
                                        type="button"
                                        class="project-image-arrow project-image-prev"
                                        aria-label="Imagen anterior"
                                        @click.stop="changeImage(-1)"
                                    >
                                        <ChevronLeft :size="19" />
                                    </button>

                                    <button
                                        type="button"
                                        class="project-image-arrow project-image-next"
                                        aria-label="Imagen siguiente"
                                        @click.stop="changeImage(1)"
                                    >
                                        <ChevronRight :size="19" />
                                    </button>
                                </template>
                            </div>

                            <div
                                v-else
                                class="project-image-placeholder"
                            >
                                <TreeDeciduous :size="42" />
                                <span>Imagen del proyecto</span>
                            </div>

                            <!-- MINIATURAS -->
                            <div
                                v-if="galleryCount > 1"
                                class="project-thumbnails"
                            >
                                <button
                                    v-for="(image, index) in gallery"
                                    :key="image.id"
                                    type="button"
                                    class="project-thumbnail"
                                    :class="{
                                        'is-active': selectedImage === image.id,
                                    }"
                                    :aria-label="`Ver imagen ${index + 1}`"
                                    :aria-pressed="selectedImage === image.id"
                                    @click="selectImage(image)"
                                >
                                    <img
                                        :src="image.url"
                                        :alt="
                                            image.alt ||
                                            `${project.name} - imagen ${index + 1}`
                                        "
                                        loading="lazy"
                                    />
                                </button>
                            </div>

                            <div
                                v-if="galleryCount"
                                class="project-gallery-footer"
                            >
                                <span>GALERÍA DEL PROYECTO</span>

                                <span class="project-gallery-counter">
                                    {{ String(galleryIndex + 1).padStart(2, '0') }}
                                    <span>/</span>
                                    {{ String(galleryCount).padStart(2, '0') }}
                                </span>
                            </div>
                        </div>

                        <!-- INFORMACIÓN -->
                        <div class="project-information">

                            <div class="project-information-top">
                                <span class="project-eyebrow">
                                    GRUPO GILANT
                                </span>

                                <span class="project-index">
                                    PROYECTO DESTACADO
                                </span>
                            </div>

                            <h1>{{ project.name }}</h1>

                            <div class="project-title-decoration">
                                <span></span>
                            </div>

                            <div
                                v-if="project.description"
                                class="project-description"
                            >
                                {{ project.description }}
                            </div>

                            <div
                                v-else-if="project.short_description"
                                class="project-description"
                            >
                                {{ project.short_description }}
                            </div>

                            <div
                                v-else
                                class="project-description"
                            >
                                En Grupo Gilant desarrollamos proyectos de
                                construcción con madera estructural y obra
                                exterior. Integramos experiencia, conocimiento
                                técnico y diseño para crear espacios que
                                responden a las necesidades de cada cliente.
                            </div>

                            <div class="project-information-divider"></div>

                            <div class="project-concept">
                                <div class="project-concept-icon">
                                    <TreeDeciduous :size="21" />
                                </div>

                                <div>
                                    <span>DISEÑO Y CONSTRUCCIÓN EN MADERA</span>

                                    <p>
                                        Desde la planeación hasta la instalación,
                                        desarrollamos soluciones a medida con
                                        materiales de calidad y respeto por
                                        el entorno natural.
                                    </p>
                                </div>
                            </div>

                            <Link
                                href="/#contacto"
                                class="project-contact-link"
                            >
                                <span>Hablemos de tu proyecto</span>

                                <span class="project-contact-icon">
                                    <ArrowUpRight :size="18" />
                                </span>
                            </Link>

                            <div class="project-information-foot">
                                <span>GRUPO GILANT</span>
                                <span>CONSTRUCCIÓN CON MADERA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <GilantFooter />

        <!-- LIGHTBOX -->
        <Transition name="project-lightbox">
            <div
                v-if="showGallery && currentImage"
                class="project-lightbox"
                role="dialog"
                aria-modal="true"
                :aria-label="`Galería de ${project.name}`"
                @click.self="closeGallery"
            >
                <button
                    type="button"
                    class="project-lightbox-close"
                    aria-label="Cerrar galería"
                    @click="closeGallery"
                >
                    <X :size="23" />
                </button>

                <div class="project-lightbox-content">

                    <div class="project-lightbox-heading">
                        <span>GRUPO GILANT</span>
                        <p>{{ project.name }}</p>
                    </div>

                    <div
                        class="project-lightbox-stage"
                        @pointerdown="handlePointerStart($event, 'lightbox')"
                        @pointerup="handlePointerEnd($event, 'lightbox')"
                        @pointercancel="handlePointerCancel($event, 'lightbox')"
                    >
                        <button
                            v-if="hasMultipleImages"
                            type="button"
                            class="project-lightbox-arrow project-lightbox-prev"
                            aria-label="Imagen anterior"
                            @click="changeImage(-1)"
                        >
                            <ChevronLeft :size="26" />
                        </button>

                        <img
                            :src="currentImage.url"
                            :alt="currentImage.alt || project.name"
                            draggable="false"
                        />

                        <button
                            v-if="hasMultipleImages"
                            type="button"
                            class="project-lightbox-arrow project-lightbox-next"
                            aria-label="Imagen siguiente"
                            @click="changeImage(1)"
                        >
                            <ChevronRight :size="26" />
                        </button>
                    </div>

                    <div class="project-lightbox-footer">
                        <span>{{ currentImage.alt || project.name }}</span>

                        <span>
                            {{ String(galleryIndex + 1).padStart(2, '0') }}
                            /
                            {{ String(galleryCount).padStart(2, '0') }}
                        </span>
                    </div>

                    <div
                        v-if="galleryCount > 1"
                        class="project-lightbox-thumbnails"
                    >
                        <button
                            v-for="image in gallery"
                            :key="image.id"
                            type="button"
                            :class="{
                                'is-active': selectedImage === image.id,
                            }"
                            :aria-label="`Ver ${image.alt || 'imagen'}`"
                            :aria-pressed="selectedImage === image.id"
                            @click="selectImage(image)"
                        >
                            <img
                                :src="image.url"
                                :alt="image.alt || project.name"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* -------------------------------------------------------------------------- */
/* CONTENEDOR GENERAL                                                          */
/* -------------------------------------------------------------------------- */

.project-detail {
    background: #fffefa;
    color: #25352d;
}

/* -------------------------------------------------------------------------- */
/* ENCABEZADO                                                                   */
/* -------------------------------------------------------------------------- */

.project-detail-section {
    padding: clamp(115px, 13vh, 155px) 0 100px;
    background: #fffefa;
}

.project-detail-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 30px;
    margin-bottom: 44px;
    padding-bottom: 22px;
    border-bottom: 1px solid #e9e9e1;
}

.project-section-label {
    display: inline-flex;
    align-items: center;
    gap: 11px;
    color: #737d43;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .17em;
}

.project-section-label > span {
    width: 22px;
    height: 1px;
    background: #737d43;
}

.project-detail-heading p {
    max-width: 430px;
    margin: 0;
    color: #92978d;
    font-size: 12px;
    line-height: 1.7;
    text-align: right;
}

/* -------------------------------------------------------------------------- */
/* DISTRIBUCIÓN PRINCIPAL                                                      */
/* -------------------------------------------------------------------------- */

.project-detail-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.08fr) minmax(300px, .82fr);
    align-items: center;
    gap: clamp(48px, 7vw, 105px);
}

.project-gallery-column {
    min-width: 0;
    width: 100%;
    max-width: 650px;
    margin: 0 auto;
}

/* -------------------------------------------------------------------------- */
/* IMAGEN PRINCIPAL                                                            */
/* -------------------------------------------------------------------------- */

.project-main-image {
    position: relative;
    overflow: hidden;
    width: 100%;
    max-height: 490px;
    aspect-ratio: 1.2 / 1;
    background: #eeeee8;
    cursor: zoom-in;
    touch-action: pan-y;
    user-select: none;
    -webkit-user-select: none;
}

.project-main-image > img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .7s cubic-bezier(.2, .7, .2, 1);
    -webkit-user-drag: none;
}

.project-main-image:hover > img {
    transform: scale(1.025);
}

.project-expand-button {
    position: absolute;
    right: 17px;
    bottom: 17px;
    display: grid;
    width: 38px;
    height: 38px;
    place-items: center;
    border: 1px solid rgb(255 255 255 / 45%);
    background: rgb(25 44 36 / 72%);
    color: white;
    cursor: pointer;
    transition: background .25s ease;
}

.project-expand-button:hover {
    background: #737d43;
}

.project-image-arrow {
    position: absolute;
    z-index: 2;
    top: 50%;
    display: grid;
    width: 38px;
    height: 38px;
    padding: 0;
    place-items: center;
    border: 1px solid rgb(255 255 255 / 45%);
    border-radius: 50%;
    background: rgb(25 44 36 / 68%);
    color: white;
    cursor: pointer;
    transform: translateY(-50%);
    transition: background .25s ease, border-color .25s ease;
}

.project-image-arrow:hover {
    border-color: white;
    background: #737d43;
}

.project-image-prev {
    left: 15px;
}

.project-image-next {
    right: 15px;
}

.project-image-placeholder {
    display: flex;
    width: 100%;
    max-height: 490px;
    aspect-ratio: 1.2 / 1;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 13px;
    background: #f1f0e9;
    color: #737d43;
}

.project-image-placeholder span {
    color: #92978d;
    font-size: 12px;
}

/* -------------------------------------------------------------------------- */
/* MINIATURAS                                                                   */
/* -------------------------------------------------------------------------- */

.project-thumbnails {
    display: flex;
    gap: 9px;
    margin-top: 13px;
    overflow-x: auto;
    padding: 2px 0 5px;
    scrollbar-width: thin;
    scrollbar-color: #c8cebd transparent;
}

.project-thumbnail {
    position: relative;
    overflow: hidden;
    width: 76px;
    height: 57px;
    flex: 0 0 auto;
    padding: 0;
    border: 1px solid transparent;
    background: #eeeee8;
    cursor: pointer;
    opacity: .65;
    transition: border-color .25s ease, opacity .25s ease;
}

.project-thumbnail:hover,
.project-thumbnail.is-active {
    border-color: #737d43;
    opacity: 1;
}

.project-thumbnail img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.project-gallery-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 18px;
    padding-top: 13px;
    border-top: 1px solid #e9e9e1;
    color: #9a9e94;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
}

.project-gallery-counter {
    display: flex;
    gap: 7px;
    color: #243b30;
    font-size: 11px;
    letter-spacing: .06em;
}

.project-gallery-counter span {
    color: #b5b9ae;
}

/* -------------------------------------------------------------------------- */
/* INFORMACIÓN DEL PROYECTO                                                    */
/* -------------------------------------------------------------------------- */

.project-information {
    width: 100%;
    max-width: 480px;
    padding: 12px 0;
}

.project-information-top {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 27px;
}

.project-eyebrow {
    color: #737d43;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .2em;
}

.project-index {
    color: #a1a59b;
    font-size: 9px;
    font-weight: 600;
    letter-spacing: .12em;
}

.project-information h1 {
    margin: 0;
    color: #243b30;
    font-family: Manrope, sans-serif;
    font-size: clamp(32px, 3.5vw, 47px);
    font-weight: 500;
    line-height: 1.16;
    letter-spacing: -.045em;
    overflow-wrap: anywhere;
}

.project-title-decoration {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 27px 0 25px;
}

.project-title-decoration span {
    width: 43px;
    height: 2px;
    background: #737d43;
}

.project-description {
    color: #737c70;
    font-size: 14px;
    line-height: 1.95;
    white-space: pre-line;
}

.project-information-divider {
    height: 1px;
    margin: 32px 0;
    background: #e9e9e1;
}

/* -------------------------------------------------------------------------- */
/* CONCEPTO                                                                     */
/* -------------------------------------------------------------------------- */

.project-concept {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.project-concept-icon {
    display: grid;
    width: 42px;
    height: 42px;
    flex: 0 0 auto;
    place-items: center;
    background: #f2f2eb;
    color: #737d43;
}

.project-concept > div:last-child > span {
    display: block;
    margin-bottom: 8px;
    color: #737d43;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
}

.project-concept p {
    margin: 0;
    color: #81887d;
    font-size: 12px;
    line-height: 1.8;
}

/* -------------------------------------------------------------------------- */
/* CONTACTO                                                                     */
/* -------------------------------------------------------------------------- */

.project-contact-link {
    display: flex;
    width: 100%;
    min-height: 58px;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-top: 35px;
    padding: 0 17px 0 21px;
    border: 1px solid #243b30;
    background: #243b30;
    color: #fffefa;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: background .25s ease, border-color .25s ease;
}

.project-contact-link:hover {
    border-color: #737d43;
    background: #737d43;
}

.project-contact-icon {
    display: grid;
    width: 31px;
    height: 31px;
    place-items: center;
    border: 1px solid rgb(255 255 255 / 30%);
    border-radius: 50%;
    transition: transform .25s ease;
}

.project-contact-link:hover .project-contact-icon {
    transform: translate(2px, -2px);
}

.project-information-foot {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
    margin-top: 25px;
    color: #a1a59b;
    font-size: 8px;
    font-weight: 600;
    letter-spacing: .1em;
}

/* -------------------------------------------------------------------------- */
/* LIGHTBOX                                                                     */
/* -------------------------------------------------------------------------- */

.project-lightbox {
    position: fixed;
    z-index: 99999;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100vw;
    height: 100vh;
    height: 100dvh;
    padding: 45px;
    overflow: hidden;
    background: rgb(17 29 23 / 97%);
}

.project-lightbox-close {
    position: absolute;
    z-index: 5;
    top: 22px;
    right: 25px;
    display: grid;
    width: 42px;
    height: 42px;
    place-items: center;
    border: 1px solid rgb(255 255 255 / 25%);
    border-radius: 50%;
    background: transparent;
    color: white;
    cursor: pointer;
    transition: background .25s ease, border-color .25s ease;
}

.project-lightbox-close:hover {
    border-color: white;
    background: rgb(255 255 255 / 12%);
}

.project-lightbox-content {
    display: flex;
    width: min(1000px, 100%);
    max-height: 100%;
    flex-direction: column;
    align-items: center;
}

.project-lightbox-heading {
    width: 100%;
    margin-bottom: 17px;
}

.project-lightbox-heading > span {
    color: #aab58e;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .2em;
}

.project-lightbox-heading p {
    margin: 7px 0 0;
    color: white;
    font-size: 15px;
    font-weight: 500;
}

.project-lightbox-stage {
    position: relative;
    display: flex;
    width: 100%;
    height: min(65vh, 680px);
    align-items: center;
    justify-content: center;
    touch-action: pan-y;
    user-select: none;
    -webkit-user-select: none;
}

.project-lightbox-stage > img {
    display: block;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    -webkit-user-drag: none;
}

.project-lightbox-arrow {
    position: absolute;
    z-index: 2;
    top: 50%;
    display: grid;
    width: 43px;
    height: 43px;
    place-items: center;
    border: 1px solid rgb(255 255 255 / 30%);
    border-radius: 50%;
    background: rgb(255 255 255 / 8%);
    color: white;
    cursor: pointer;
    transform: translateY(-50%);
    transition: background .25s ease;
}

.project-lightbox-arrow:hover {
    background: rgb(255 255 255 / 20%);
}

.project-lightbox-prev {
    left: 0;
}

.project-lightbox-next {
    right: 0;
}

.project-lightbox-footer {
    display: flex;
    width: 100%;
    justify-content: space-between;
    gap: 20px;
    padding: 16px 0;
    border-bottom: 1px solid rgb(255 255 255 / 15%);
    color: rgb(255 255 255 / 65%);
    font-size: 11px;
}

.project-lightbox-thumbnails {
    display: flex;
    max-width: 100%;
    gap: 9px;
    overflow-x: auto;
    padding: 15px 0 5px;
}

.project-lightbox-thumbnails button {
    overflow: hidden;
    width: 65px;
    height: 48px;
    flex: 0 0 auto;
    padding: 0;
    border: 1px solid transparent;
    background: transparent;
    cursor: pointer;
    opacity: .45;
    transition: opacity .2s ease, border-color .2s ease;
}

.project-lightbox-thumbnails button.is-active {
    border-color: #dce2d4;
    opacity: 1;
}

.project-lightbox-thumbnails img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* -------------------------------------------------------------------------- */
/* TRANSICIÓN                                                                   */
/* -------------------------------------------------------------------------- */

.project-lightbox-enter-active,
.project-lightbox-leave-active {
    transition: opacity .25s ease;
}

.project-lightbox-enter-from,
.project-lightbox-leave-to {
    opacity: 0;
}

/* -------------------------------------------------------------------------- */
/* RESPONSIVE                                                                   */
/* -------------------------------------------------------------------------- */

@media (max-width: 1000px) {
    .project-detail-grid {
        grid-template-columns: minmax(0, 1fr) minmax(280px, .85fr);
        gap: 40px;
    }

    .project-main-image {
        max-height: 420px;
    }

    .project-information h1 {
        font-size: clamp(30px, 4vw, 40px);
    }
}

@media (max-width: 800px) {
    .project-detail-grid {
        grid-template-columns: 1fr;
        gap: 42px;
    }

    .project-gallery-column {
        max-width: 620px;
    }

    .project-information {
        max-width: 620px;
        margin: 0 auto;
    }

    .project-main-image {
        max-height: 480px;
    }

    .project-detail-section {
        padding-bottom: 70px;
    }
}

@media (max-width: 600px) {
    .project-detail-section {
        padding-top: 105px;
    }

    .project-detail-heading {
        align-items: flex-start;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 28px;
    }

    .project-detail-heading p {
        text-align: left;
    }

    .project-detail-grid {
        gap: 35px;
    }

    .project-main-image {
        aspect-ratio: 1.05 / 1;
    }

    .project-image-arrow {
        width: 34px;
        height: 34px;
    }

    .project-image-prev {
        left: 10px;
    }

    .project-image-next {
        right: 10px;
    }

    .project-expand-button {
        right: 11px;
        bottom: 11px;
        width: 34px;
        height: 34px;
    }

    .project-thumbnail {
        width: 65px;
        height: 49px;
    }

    .project-information {
        padding: 0;
    }

    .project-information-top {
        margin-bottom: 20px;
    }

    .project-information h1 {
        font-size: 34px;
    }

    .project-title-decoration {
        margin: 22px 0;
    }

    .project-description {
        font-size: 13px;
    }

    .project-information-divider {
        margin: 25px 0;
    }

    .project-information-foot {
        flex-direction: column;
    }

    .project-lightbox {
        padding: 65px 13px 20px;
    }

    .project-lightbox-stage {
        height: 55vh;
    }

    .project-lightbox-arrow {
        width: 35px;
        height: 35px;
    }

    .project-lightbox-close {
        top: 13px;
        right: 13px;
        width: 38px;
        height: 38px;
    }

    .project-lightbox-thumbnails button {
        width: 55px;
        height: 42px;
    }
}

/* -------------------------------------------------------------------------- */
/* ACCESIBILIDAD                                                                */
/* -------------------------------------------------------------------------- */

@media (prefers-reduced-motion: reduce) {
    .project-detail *,
    .project-detail *::before,
    .project-detail *::after {
        scroll-behavior: auto !important;
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
}
</style>
