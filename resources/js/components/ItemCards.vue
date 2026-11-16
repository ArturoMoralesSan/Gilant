
<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import {
    ArrowRight,
    ArrowUpRight,
    ChevronLeft,
    ChevronRight,
    TreeDeciduous,
} from 'lucide-vue-next'

import ImageGallery from '@/components/ImageGallery.vue'

type SectionPadding = 'none' | 'sm' | 'md' | 'lg' | 'xl'
type GalleryPosition = 'left' | 'right'

interface ProductVariant {
    id: number
    name: string
}

interface Product {
    id: number
    name: string
    slug?: string
    short_description?: string | null
    image_url?: string | null
    image_alt?: string | null
    variants?: ProductVariant[]
}

interface ItemCard {
    id?: number | string
    number?: string
    title?: string
    name?: string
    description?: string | null
    short_description?: string | null
    image?: string | null
    image_url?: string | null
    imageAlt?: string
    image_alt?: string | null
    href?: string
    slug?: string
    variants?: ProductVariant[]
}

const props = withDefaults(defineProps<{
    variant?: 'grid' | 'list' | 'featured'
    items?: ItemCard[]
    products?: Product[]
    eyebrow?: string
    title?: string
    headingLevel?: 1 | 2
    linkText?: string
    linkHref?: string
    emptyText?: string
    image?: string | null
    imageAlt?: string
    actionLabel?: string
    compact?: boolean
    background?: string
    sectionPadding?: SectionPadding
    galleryPosition?: GalleryPosition
}>(), {
    variant: 'grid',
    items: () => [],
    products: () => [],
    eyebrow: '',
    title: '',
    headingLevel: 2,
    linkText: '',
    linkHref: '#contacto',
    emptyText: 'Estamos preparando esta sección.',
    image: null,
    imageAlt: '',
    actionLabel: 'Conocer más',
    compact: false,
    background: '#f7f4ec',
    sectionPadding: 'md',
    galleryPosition: 'right',
})

const emit = defineEmits<{
    select: [item: ItemCard]
}>()

const sectionStyle = computed(() => ({
    '--section-background': props.background,
}))

const sectionClasses = computed(() => [
    `section-padding-${props.sectionPadding}`,
])

// -----------------------------------------------------------------------------
// PRODUCTOS
// -----------------------------------------------------------------------------

const productItems = computed<ItemCard[]>(() => {
    const source: ItemCard[] = props.products.length
        ? props.products.map((product, index) => ({
            id: product.id,
            number: String(index + 1).padStart(2, '0'),
            title: product.name,
            description: product.short_description ?? '',
            image: product.image_url ?? null,
            imageAlt: product.image_alt ?? '',
            href: product.slug ? `/productos/${product.slug}` : undefined,
            variants: product.variants ?? [],
        }))
        : props.items

    return source.map((product, index) => ({
        ...product,
        number: product.number || String(index + 1).padStart(2, '0'),
        title: product.title || product.name || '',
        description: product.short_description ?? product.description ?? '',
        image: product.image_url ?? product.image ?? null,
        imageAlt: product.image_alt ?? product.imageAlt ?? '',
        href: product.href || (
            product.slug ? `/productos/${product.slug}` : undefined
        ),
        variants: product.variants ?? [],
    }))
})

// -----------------------------------------------------------------------------
// PROYECTOS DESTACADOS
// Recorre todos los elementos recibidos en props.items.
// -----------------------------------------------------------------------------

const featuredIndex = ref(0)

const featuredItem = computed(() => {
    return props.items[featuredIndex.value] ?? null
})

const hasMultipleFeatured = computed(() => props.items.length > 1)

const featuredCounter = computed(() => ({
    current: String(featuredIndex.value + 1).padStart(2, '0'),
    total: String(props.items.length).padStart(2, '0'),
}))

function nextFeatured() {
    if (!props.items.length) return

    featuredIndex.value = (featuredIndex.value + 1) % props.items.length
}

function previousFeatured() {
    if (!props.items.length) return

    featuredIndex.value =
        (featuredIndex.value - 1 + props.items.length) % props.items.length
}

watch(
    () => props.items,
    () => {
        featuredIndex.value = 0
    },
)

// -----------------------------------------------------------------------------
// IMAGEN DEL PROYECTO ACTUAL
// -----------------------------------------------------------------------------

const featuredGallery = computed(() => {
    const item = featuredItem.value

    if (item?.image || item?.image_url) {
        return [{
            src: (item.image || item.image_url)!,
            alt: item.imageAlt || item.image_alt || item.title || item.name || '',
        }]
    }

    if (props.image) {
        return [{
            src: props.image,
            alt: props.imageAlt || props.title,
        }]
    }

    return []
})
</script>

<template>
    <!-- =====================================================
         GRID / PRODUCTOS
    ====================================================== -->
    <section
        v-if="props.variant === 'grid'"
        :class="[
            'husky-products',
            'husky-section',
            ...sectionClasses,
            { 'husky-section-compact': props.compact },
        ]"
        :style="sectionStyle"
    >
        <div class="husky-container">
            <div class="husky-section-heading">
                <div>
                    <p class="husky-eyebrow">{{ props.eyebrow }}</p>

                    <component
                        :is="`h${props.headingLevel}`"
                        class="husky-section-title"
                    >
                        {{ props.title }}
                    </component>
                </div>

                <a
                    v-if="props.linkText"
                    class="husky-text-link"
                    :href="props.linkHref"
                >
                    {{ props.linkText }}
                    <ArrowRight :size="16" />
                </a>
            </div>

            <slot name="intro" />
        </div>

        <div v-if="productItems.length" class="husky-product-grid">
            <a
                v-for="(item, index) in productItems"
                :key="item.id ?? index"
                class="husky-product-card"
                :href="item.href || '#contacto'"
                @click="emit('select', item)"
            >
                <div class="husky-product-image">
                    <img
                        v-if="item.image"
                        :src="item.image"
                        :alt="item.imageAlt || item.title || ''"
                        loading="lazy"
                    />

                    <div
                        v-else
                        class="husky-product-image-empty"
                        role="img"
                        :aria-label="`Imagen no disponible para ${item.title}`"
                    >
                        <TreeDeciduous :size="42" stroke-width="1.2" />
                    </div>

                    <span>{{ item.number }}</span>
                </div>

                <div class="gilant-product-info">
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.description }}</p>

                    <div
                        v-if="item.variants?.length"
                        class="husky-product-variants"
                    >
                        <span class="husky-variants-label">
                            Disponibles en
                        </span>

                        <div class="husky-variants-list">
                            <span
                                v-for="productVariant in item.variants"
                                :key="productVariant.id"
                                class="husky-variant-badge"
                            >
                                {{ productVariant.name }}
                            </span>
                        </div>
                    </div>

                    <ArrowUpRight
                        class="husky-card-arrow"
                        :size="19"
                    />
                </div>
            </a>
        </div>

        <div v-else class="husky-products-empty">
            <TreeDeciduous :size="34" stroke-width="1.3" />
            <p>{{ props.emptyText }}</p>
        </div>
    </section>

    <!-- =====================================================
         LIST / SERVICIOS
    ====================================================== -->
    <section
        v-else-if="props.variant === 'list'"
        :class="[
            'husky-services',
            'husky-section',
            ...sectionClasses,
        ]"
        :style="sectionStyle"
    >
        <div class="husky-container husky-services-grid">
            <div class="husky-service-list">
                <a
                    v-for="(item, index) in props.items"
                    :key="item.id ?? index"
                    class="husky-service-item"
                    :href="item.href || props.linkHref"
                    :aria-label="`Conocer más sobre ${item.title || item.name}`"
                >
                    <span class="husky-service-number">
                        {{ item.number || String(index + 1).padStart(2, '0') }}
                    </span>

                    <div class="husky-service-content">
                        <h3>{{ item.title || item.name }}</h3>
                        <p>{{ item.description }}</p>
                    </div>

                    <span class="husky-service-arrow" aria-hidden="true">
                        <ArrowUpRight :size="19" />
                    </span>
                </a>

                <div v-if="!props.items.length" class="husky-service-empty">
                    <p>{{ props.emptyText }}</p>
                </div>

                <a
                    v-if="props.linkText"
                    class="husky-services-more"
                    :href="props.linkHref"
                >
                    <span>
                        <slot name="footer">¿Buscas otro servicio?</slot>
                    </span>

                    <strong>
                        {{ props.linkText }}
                        <ArrowRight :size="15" />
                    </strong>
                </a>
            </div>

            <div class="husky-services-intro">
                <span class="husky-services-kicker">
                    <span class="husky-services-kicker-dot"></span>
                    {{ props.eyebrow }}
                </span>

                <component
                    :is="`h${props.headingLevel}`"
                    class="husky-section-title"
                >
                    {{ props.title }}
                </component>

                <slot name="intro" />
                <slot name="caption" />
            </div>
        </div>
    </section>

    <!-- =====================================================
         FEATURED / PROYECTOS DESTACADOS
    ====================================================== -->
    <section
        v-else-if="props.variant === 'featured'"
        :class="[
            'gilant-about',
            {
                'gilant-about--gallery-left':
                    props.galleryPosition === 'left',
            },
        ]"
        :style="sectionStyle"
    >
        <!-- TEXTO DEL PROYECTO -->
        <div class="gilant-about-info">
            <div
                :class="[
                    'gilant-about-info-inner',
                    ...sectionClasses,
                ]"
            >
                <div class="gilant-about-eyebrow">
                    <span class="gilant-about-eyebrow-line"></span>
                    {{ props.eyebrow || 'PROYECTO DESTACADO' }}
                </div>

                <component
                    :is="`h${props.headingLevel}`"
                    :key="`featured-title-${featuredItem?.id ?? featuredIndex}`"
                    class="gilant-about-title"
                >
                    {{
                        featuredItem?.title ||
                        featuredItem?.name ||
                        props.title
                    }}
                </component>

                <div
                    :key="`featured-description-${featuredItem?.id ?? featuredIndex}`"
                    class="gilant-about-description"
                >
                    <p>
                        {{
                            featuredItem?.description ||
                            featuredItem?.short_description ||
                            'Conoce más sobre este proyecto de Grupo Gilant.'
                        }}
                    </p>
                </div>

                <!-- ENLACE AL DETALLE DEL PROYECTO -->
                <a
                    v-if="featuredItem?.href || props.linkHref"
                    :key="`featured-link-${featuredItem?.id ?? featuredIndex}`"
                    class="gilant-about-action"
                    :href="featuredItem?.href || props.linkHref"
                >
                    <span>{{ props.actionLabel }}</span>

                    <span class="gilant-about-action-icon">
                        <ArrowRight :size="16" />
                    </span>
                </a>

                <div class="gilant-about-signature">
                    <span></span>
                    GRUPO GILANT
                </div>
            </div>
        </div>

        <!-- IMAGEN DEL PROYECTO ACTUAL -->
        <div class="gilant-about-gallery">
            <Transition name="featured-image" mode="out-in">
                <ImageGallery
                    v-if="featuredGallery.length"
                    :key="`featured-gallery-${featuredItem?.id ?? featuredIndex}`"
                    :items="featuredGallery"
                    :autoplay="false"
                    :interval="5000"
                />

                <div
                    v-else
                    :key="`featured-empty-${featuredIndex}`"
                    class="gilant-about-gallery-empty"
                    role="img"
                    :aria-label="`Imagen no disponible para ${featuredItem?.title || props.title}`"
                >
                    <TreeDeciduous :size="42" stroke-width="1.2" />
                </div>
            </Transition>

            <!-- CONTROLES DEL CARRUSEL -->
            <div
                v-if="hasMultipleFeatured"
                class="gilant-featured-controls"
            >
                <span class="gilant-featured-counter">
                    {{ featuredCounter.current }}
                    <span>/ {{ featuredCounter.total }}</span>
                </span>

                <div class="gilant-featured-arrows">
                    <button
                        type="button"
                        aria-label="Proyecto anterior"
                        @click="previousFeatured"
                    >
                        <ChevronLeft :size="19" />
                    </button>

                    <button
                        type="button"
                        aria-label="Siguiente proyecto"
                        @click="nextFeatured"
                    >
                        <ChevronRight :size="19" />
                    </button>
                </div>
            </div>

            <div class="gilant-about-gallery-caption">
                NATURALEZA · DISEÑO · DURABILIDAD
            </div>
        </div>
    </section>
</template>

<style scoped>
/* =========================================================
   SECCIONES Y CONTENEDORES
========================================================= */

.husky-section {
    width: 100%;
    background: var(--section-background, var(--gilant-bg-soft));
}

.husky-container {
    width: var(--gilant-container-width);
    max-width: var(--gilant-content-max);
    margin-inline: auto;
}

.section-padding-none {
    padding-block: 0;
}

.section-padding-sm {
    padding-block: var(--gilant-section-padding-sm);
}

.section-padding-md {
    padding-block: var(--gilant-section-padding);
}

.section-padding-lg {
    padding-block: var(--gilant-section-padding-lg);
}

.section-padding-xl {
    padding-block: clamp(110px, 12vw, 170px);
}

.husky-section-compact {
    padding-block: clamp(35px, 4vw, 60px);
}

.husky-section-heading {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 30px;
    margin-bottom: 38px;
}

.husky-eyebrow {
    margin: 0 0 12px;
    color: var(--gilant-olive);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
}

.husky-section-title {
    margin: 0;
    color: var(--gilant-forest-dark);
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(32px, 4vw, 48px);
    font-weight: 400;
    line-height: 1.12;
}

.husky-text-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: var(--gilant-forest);
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: color 0.25s ease;
}

.husky-text-link:hover {
    color: var(--gilant-gold);
}

/* =========================================================
   GRID / PRODUCTOS
========================================================= */

.husky-product-grid {
    display: grid;
    width: 100%;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0;
    margin: 0;
    padding: 0;
}

.husky-product-card {
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
    color: inherit;
    background: var(--gilant-white);
    text-decoration: none;
    transition: box-shadow 0.3s ease;
}

.husky-product-card:hover {
    box-shadow: var(--gilant-shadow-hover);
}

/* =========================================================
   IMÁGENES DE PRODUCTOS
========================================================= */

.husky-product-image {
    position: relative;
    overflow: hidden;
    width: 100%;
    aspect-ratio: 1.25 / 1;
    flex-shrink: 0;
    background: #e8e8df;
}

.husky-product-image img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.husky-product-card:hover .husky-product-image img {
    transform: scale(1.04);
}

.husky-product-image > span {
    position: absolute;
    top: 17px;
    left: 18px;
    color: var(--gilant-white);
    font-size: 11px;
    letter-spacing: 0.12em;
    text-shadow: 0 1px 5px rgba(0, 0, 0, 0.4);
}

.husky-product-image-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: var(--gilant-olive);
    background: var(--gilant-bg-soft);
}

/* =========================================================
   INFORMACIÓN GILANT
   ALTURA UNIFORME EN TODAS LAS TARJETAS
========================================================= */

.gilant-product-info {
    position: relative;
    display: flex;
    flex-direction: column;
    flex: 1;
    height: 230px;
    min-height: 230px;
    box-sizing: border-box;
    padding: 24px 24px 28px;
    color: var(--gilant-cream);
    background: var(--gilant-forest);
    transition: background 0.3s ease;
}

.husky-product-card:hover .gilant-product-info {
    background: var(--gilant-forest-dark);
}

.gilant-product-info h3 {
    margin: 0 30px 9px 0;
    color: var(--gilant-cream);
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 22px;
    font-weight: 400;
}

.gilant-product-info > p {
    display: -webkit-box;
    overflow: hidden;
    margin: 0;
    color: rgba(247, 244, 236, 0.78);
    font-size: 13px;
    line-height: 1.7;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
}

.gilant-product-info .husky-card-arrow {
    position: absolute;
    top: 25px;
    right: 22px;
    color: var(--gilant-gold);
    transition: transform 0.25s ease;
}

.husky-product-card:hover .gilant-product-info .husky-card-arrow {
    transform: translate(3px, -3px);
}

.husky-product-variants {
    margin-top: auto;
    padding-top: 14px;
}

.husky-variants-label {
    display: block;
    margin-bottom: 9px;
    color: var(--gilant-gold);
    font-size: 10px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.husky-variants-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    max-height: 48px;
    overflow: hidden;
}

.husky-variant-badge {
    padding: 5px 9px;
    border: 1px solid rgba(196, 154, 90, 0.45);
    color: var(--gilant-cream);
    background: transparent;
    font-size: 10px;
}

/* =========================================================
   ESTADO VACÍO
========================================================= */

.husky-products-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 180px;
    flex-direction: column;
    gap: 12px;
    color: var(--gilant-olive);
    background: var(--gilant-bg-soft);
}

.husky-products-empty p {
    margin: 0;
    font-size: 13px;
}

/* =========================================================
   SERVICIOS
========================================================= */

.husky-services {
    width: 100%;
    padding-block: var(--gilant-section-padding);
    background: var(--gilant-forest);
}

.husky-services .husky-container {
    width: var(--gilant-container-width);
    max-width: var(--gilant-content-max);
    margin-inline: auto;
}

.husky-services-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
    grid-template-areas: "list intro";
    gap: clamp(35px, 5vw, 65px);
    align-items: start;
    width: 100%;
}

.husky-services-intro {
    grid-area: intro;
    position: sticky;
    top: 110px;
    margin: 0;
}

.husky-services .husky-section-title {
    color: var(--gilant-cream);
}

.husky-services-kicker {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    color: var(--gilant-gold);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.17em;
    text-transform: uppercase;
}

.husky-services-kicker-dot {
    width: 7px;
    height: 7px;
    flex: 0 0 7px;
    border-radius: 50%;
    background: var(--gilant-gold);
}

.husky-services-intro :deep(p) {
    color: rgba(247, 244, 236, 0.72);
    font-size: 13px;
    line-height: 1.8;
}

.husky-service-list {
    grid-area: list;
    width: 100%;
    border-top: 1px solid rgba(247, 244, 236, 0.2);
}

.husky-service-item {
    display: grid;
    grid-template-columns: 34px minmax(0, 1fr) 34px;
    align-items: center;
    gap: 18px;
    padding: 21px 4px;
    border-bottom: 1px solid rgba(247, 244, 236, 0.2);
    color: inherit;
    text-decoration: none;
    transition:
        background 0.25s ease,
        padding 0.25s ease;
}

.husky-service-item:hover {
    padding-inline: 12px;
    background: rgba(255, 255, 255, 0.045);
}

.husky-service-number {
    color: var(--gilant-gold);
    font-size: 11px;
    letter-spacing: 0.08em;
}

.husky-service-content h3 {
    margin: 0 0 6px;
    color: var(--gilant-cream);
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 20px;
    font-weight: 400;
}

.husky-service-content p {
    margin: 0;
    color: rgba(247, 244, 236, 0.65);
    font-size: 12px;
    line-height: 1.65;
}

.husky-service-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: 1px solid rgba(196, 154, 90, 0.55);
    border-radius: 50%;
    color: var(--gilant-gold);
    transition: all 0.25s ease;
}

.husky-service-item:hover .husky-service-arrow {
    color: var(--gilant-forest);
    background: var(--gilant-gold);
    border-color: var(--gilant-gold);
}

.husky-service-empty {
    padding: 25px 0;
    color: rgba(247, 244, 236, 0.7);
    font-size: 13px;
}

.husky-services-more {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding-top: 24px;
    color: rgba(247, 244, 236, 0.65);
    font-size: 12px;
    text-decoration: none;
}

.husky-services-more strong {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--gilant-gold);
    font-weight: 600;
}

/* =========================================================
   FEATURED / NOSOTROS
========================================================= */

.gilant-about {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    width: 100%;
    overflow: hidden;
    background: var(--section-background, var(--gilant-bg-soft));
}

.gilant-about-info {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 0;
    padding-inline: clamp(28px, 4vw, 60px);
    background: transparent;
}

.gilant-about-info-inner {
    width: min(100%, 540px);
}

.gilant-about-info-inner.section-padding-none {
    padding-block: 0;
}

.gilant-about-info-inner.section-padding-sm {
    padding-block: var(--gilant-section-padding-sm);
}

.gilant-about-info-inner.section-padding-md {
    padding-block: var(--gilant-section-padding);
}

.gilant-about-info-inner.section-padding-lg {
    padding-block: var(--gilant-section-padding-lg);
}

.gilant-about-info-inner.section-padding-xl {
    padding-block: clamp(110px, 12vw, 170px);
}

.gilant-about-eyebrow {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    color: var(--gilant-olive);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
}

.gilant-about-eyebrow-line {
    width: 27px;
    height: 1px;
    flex: 0 0 27px;
    background: var(--gilant-gold);
}

.gilant-about-title {
    max-width: 520px;
    margin: 0;
    color: var(--gilant-forest-dark);
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(29px, 3vw, 42px);
    font-weight: 400;
    line-height: 1.08;
    letter-spacing: -0.025em;
}

.gilant-about-description {
    max-width: 500px;
    margin-top: 15px;
    color: var(--gilant-text);
    font-size: 13px;
    line-height: 1.75;
}

.gilant-about-description p {
    margin: 0;
}

.gilant-about-action {
    display: inline-flex;
    align-items: center;
    gap: 13px;
    margin-top: 20px;
    color: var(--gilant-forest);
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
}

.gilant-about-action-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border: 1px solid rgba(18, 61, 45, 0.3);
    border-radius: 50%;
    transition: all 0.25s ease;
}

.gilant-about-action:hover .gilant-about-action-icon {
    color: var(--gilant-white);
    background: var(--gilant-forest);
    border-color: var(--gilant-forest);
    transform: translateX(3px);
}

.gilant-about-signature {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-top: 22px;
    color: var(--gilant-text-muted);
    font-size: 9px;
    font-weight: 600;
    letter-spacing: 0.17em;
}

.gilant-about-signature span {
    width: 25px;
    height: 1px;
    background: var(--gilant-gold);
}

/* =========================================================
   GALERÍA
========================================================= */

.gilant-about-gallery {
    position: relative;
    width: 100%;
    min-width: 0;
    height: clamp(700px, 30vw, 700px);
    min-height: 700px;
    overflow: hidden;
    background: #e5e6dd;
}

.gilant-about-gallery::before,
.gilant-about-gallery::after {
    display: none !important;
    content: none !important;
}

.gilant-about-gallery :deep(.image-gallery),
.gilant-about-gallery :deep(.gallery),
.gilant-about-gallery :deep(.gallery-container),
.gilant-about-gallery :deep(.gallery-track),
.gilant-about-gallery :deep(.gallery-slide) {
    width: 100%;
    height: 100%;
    min-height: 0;
}

.gilant-about-gallery :deep(img) {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: none !important;
}

.gilant-about-gallery-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    min-height: 360px;
    color: var(--gilant-olive);
    background: var(--gilant-bg-soft);
}

.gilant-about-gallery-caption {
    position: absolute;
    right: 22px;
    bottom: 20px;
    z-index: 2;
    color: var(--gilant-white);
    font-size: 9px;
    font-weight: 600;
    letter-spacing: 0.16em;
    text-shadow: 0 1px 5px rgba(0, 0, 0, 0.35);
    pointer-events: none;
}

/* GALERÍA A LA IZQUIERDA */

.gilant-about--gallery-left .gilant-about-gallery {
    grid-column: 1;
    grid-row: 1;
}

.gilant-about--gallery-left .gilant-about-info {
    grid-column: 2;
    grid-row: 1;
}

/* =========================================================
   TABLET
========================================================= */

@media (max-width: 1024px) {
    .husky-product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .husky-services {
        padding-block: 60px;
    }

    .husky-services-grid {
        gap: 40px;
    }

    .gilant-about-info {
        padding-inline: 30px;
    }

    .gilant-about-title {
        font-size: 34px;
    }

    .gilant-about-gallery {
        min-height: 320px;
    }

    .gilant-about-gallery-empty {
        min-height: 320px;
    }
}

/* =========================================================
   MÓVIL
========================================================= */

@media (max-width: 768px) {
    .husky-section-heading {
        align-items: flex-start;
        flex-direction: column;
        gap: 18px;
    }

    .gilant-product-info {
        height: 220px;
        min-height: 220px;
        padding: 18px;
    }

    .gilant-product-info h3 {
        font-size: 19px;
    }

    .husky-services {
        padding-block: 48px;
    }

    .husky-services-grid {
        grid-template-columns: minmax(0, 1fr);
        grid-template-areas:
            "list"
            "intro";
        gap: 32px;
    }

    .husky-services-intro {
        position: static;
    }

    .gilant-about {
        grid-template-columns: minmax(0, 1fr);
    }

    .gilant-about-info,
    .gilant-about--gallery-left .gilant-about-info {
        grid-column: 1;
        grid-row: 1;
        min-height: 0;
        padding-inline: max(4%, 22px);
    }

    .gilant-about-gallery,
    .gilant-about--gallery-left .gilant-about-gallery {
        grid-column: 1;
        grid-row: 2;
        height: 280px;
        min-height: 280px;
    }

    .gilant-about-gallery-empty {
        height: 280px;
        min-height: 280px;
    }

    .gilant-about-info-inner {
        width: min(100%, 600px);
    }

    .gilant-about-title {
        font-size: clamp(30px, 7vw, 40px);
    }
}

/* =========================================================
   MÓVIL PEQUEÑO
========================================================= */

@media (max-width: 480px) {
    .husky-product-grid {
        grid-template-columns: 1fr;
    }

    .gilant-product-info {
        height: 210px;
        min-height: 210px;
    }

    .husky-services {
        padding-block: 40px;
    }

    .husky-service-item {
        grid-template-columns: 28px minmax(0, 1fr) 32px;
        gap: 12px;
        padding: 19px 0;
    }

    .husky-service-content h3 {
        font-size: 18px;
    }

    .husky-services-more {
        align-items: flex-start;
        flex-direction: column;
    }

    .gilant-about-info,
    .gilant-about--gallery-left .gilant-about-info {
        padding-inline: 22px;
    }

    .gilant-about-gallery,
    .gilant-about--gallery-left .gilant-about-gallery {
        height: 230px;
        min-height: 230px;
    }

    .gilant-about-gallery-empty {
        height: 230px;
        min-height: 230px;
    }

    .gilant-about-gallery-caption {
        right: 15px;
        bottom: 15px;
        font-size: 8px;
    }
}

/* =========================================================
   CONTROLES DEL CARRUSEL DE PROYECTOS
========================================================= */

.gilant-featured-controls {
    position: absolute;
    right: 22px;
    bottom: 55px;
    z-index: 5;
    display: flex;
    align-items: center;
    gap: 16px;
}

.gilant-featured-counter {
    color: var(--gilant-white);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-shadow: 0 1px 5px rgba(0, 0, 0, 0.5);
}

.gilant-featured-counter span {
    color: rgba(255, 255, 255, 0.7);
}

.gilant-featured-arrows {
    display: flex;
    align-items: center;
    gap: 8px;
}

.gilant-featured-arrows button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    padding: 0;
    border: 1px solid rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    color: var(--gilant-white);
    background: rgba(25, 44, 36, 0.55);
    cursor: pointer;
    transition: all 0.25s ease;
}

.gilant-featured-arrows button:hover {
    color: var(--gilant-forest);
    background: var(--gilant-cream);
    border-color: var(--gilant-cream);
}

.gilant-featured-arrows button:focus-visible {
    outline: 2px solid var(--gilant-gold);
    outline-offset: 3px;
}

.featured-image-enter-active,
.featured-image-leave-active {
    transition: opacity 0.25s ease;
}

.featured-image-enter-from,
.featured-image-leave-to {
    opacity: 0;
}

@media (max-width: 480px) {
    .gilant-featured-controls {
        right: 15px;
        bottom: 48px;
        gap: 10px;
    }

    .gilant-featured-arrows button {
        width: 36px;
        height: 36px;
    }
}
</style>
