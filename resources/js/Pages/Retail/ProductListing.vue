<template>
    <RetailerNavbar />
    <div class="page-container">
        <div class="content-container">
            <!-- Newly added Product Search Component -->
            <ProductSearchBar @search="handleSearch" @region-change="handleRegionChange"
                @filter-change="handleFilterChange" @apply-filters="applyAllFilters" />

            <h1 class="page-title">All Products in: <span class="location">{{ currentRegion }}</span></h1>

            <div class="product-grid">
                <!-- Row 1 -->
                <div class="product-column" v-for="(product, index) in filteredProductsRow1" :key="'row1-' + index">
                    <ProductCard :product="product" />
                </div>

                <!-- Row 2 -->
                <div class="product-column" v-for="(product, index) in filteredProductsRow2" :key="'row2-' + index">
                    <ProductCard :product="product" />
                </div>

                <!-- Row 3 -->
                <div class="product-column" v-for="(product, index) in filteredProductsRow3" :key="'row3-' + index">
                    <ProductCard :product="product" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ProductCard from './Components/ProductCard.vue';
import ProductSearchBar from './Components/ProductSearchBar.vue';
import RetailerNavbar from './Components/RetailerNavbar.vue';

export default {
    name: 'ProductListingPage',
    components: {
        ProductCard,
        ProductSearchBar,
        RetailerNavbar
    },
    data() {
        return {
            currentRegion: 'Meru',
            searchQuery: '',
            filterType: 'new',
            // Base product template
            baseProduct: {
                weight: '14 Kilograms',
                type: 'Fertilizer',
                description: 'A versatile, nutrient-rich plant food designed to promote healthy growth for a wide variety of plants, including flowers, vegetables, shrubs, and lawns. With a balanced blend of essential nutrients.',
                npkRatio: '10-10-10',
                coverage: 'Up to 140 sq. meters',
                supplier: 'Aden Agri Supplies',
                inventoryCount: 163,
                basePrice: 3450,
                currency: 'Ksh',
                minOrder: 1,
                maxOrder: 50,
                imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg',
            },
            // First row products
            productsRow1: [
                {
                    name: 'All Purpose Fertilizer',
                    weight: '14 Kilograms',
                    type: 'Fertilizer',
                    description: 'A versatile, nutrient-rich plant food designed to promote healthy growth for a wide variety of plants, including flowers, vegetables, shrubs, and lawns. With a balanced blend of essential nutrients.',
                    npkRatio: '10-10-10',
                    coverage: 'Up to 140 sq. meters',
                    supplier: 'Aden Agri Supplies',
                    inventoryCount: 163,
                    inStock: true,
                    basePrice: 3450,
                    currency: 'Ksh',
                    minOrder: 1,
                    maxOrder: 50,
                    imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg',
                    promotion: {
                        discount: '50% OFF',
                        endDate: new Date('2025-03-23')
                    }
                },
                {
                    name: 'Garden Fertilizer Plus',
                    weight: '10 Kilograms',
                    type: 'Fertilizer',
                    description: 'Enhanced formula with micronutrients for vibrant flowers and stronger vegetable plants. Ideal for home gardens and small landscaping projects.',
                    npkRatio: '15-5-10',
                    coverage: 'Up to 100 sq. meters',
                    supplier: 'Aden Agri Supplies',
                    inventoryCount: 95,
                    inStock: true,
                    basePrice: 2950,
                    currency: 'Ksh',
                    minOrder: 1,
                    maxOrder: 50,
                    imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
                },
                {
                    name: 'Premium Lawn Feed',
                    weight: '20 Kilograms',
                    type: 'Lawn Care',
                    description: 'Specialized formula for lush, green lawns. Contains extra nitrogen and iron for deep green color and stronger roots.',
                    npkRatio: '20-5-8',
                    coverage: 'Up to 200 sq. meters',
                    supplier: 'Aden Agri Supplies',
                    inventoryCount: 0,
                    inStock: false,
                    basePrice: 4750,
                    currency: 'Ksh',
                    minOrder: 1,
                    maxOrder: 30,
                    imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
                }
            ],
            // Second row products
            productsRow2: [
                {
                    name: 'Vegetable Booster',
                    weight: '15 Kilograms',
                    type: 'Fertilizer',
                    description: 'Specialized formula for tomatoes, peppers, and other fruiting vegetables. Boosts yield and improves fruit quality and taste.',
                    npkRatio: '5-10-15',
                    coverage: 'Up to 150 sq. meters',
                    supplier: 'Green Growth Ltd',
                    inventoryCount: 78,
                    inStock: true,
                    basePrice: 3850,
                    currency: 'Ksh',
                    minOrder: 1,
                    maxOrder: 40,
                    imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
                },
                {
                    name: 'Rose & Flower Food',
                    weight: '8 Kilograms',
                    type: 'Specialty',
                    description: 'Perfect formula for roses and flowering plants. Enhances bloom size, color, and fragrance while strengthening plant structure.',
                    npkRatio: '6-12-6',
                    coverage: 'Up to 80 sq. meters',
                    supplier: 'Green Growth Ltd',
                    inventoryCount: 0,
                    inStock: false,
                    basePrice: 3200,
                    currency: 'Ksh',
                    minOrder: 1,
                    maxOrder: 30,
                    imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
                },
                {
                    name: 'Citrus Tree Formula',
                    weight: '12 Kilograms',
                    type: 'Specialty',
                    description: 'Specially formulated for citrus trees and fruit-bearing plants. Promotes healthy fruit development and increased yield.',
                    npkRatio: '8-3-9',
                    coverage: 'Up to 120 sq. meters',
                    supplier: 'Green Growth Ltd',
                    inventoryCount: 42,
                    inStock: true,
                    basePrice: 4100,
                    currency: 'Ksh',
                    minOrder: 1,
                    maxOrder: 25,
                    imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg',
                    promotion: {
                        discount: '30% OFF',
                        endDate: new Date('2025-04-15')
                    }
                }
            ],
            // Third row products
            productsRow3: [
                {
                    name: 'Organic Compost',
                    weight: '25 Kilograms',
                    type: 'Organic',
                    description: 'All-natural compost made from plant materials. Improves soil structure, drainage, and microbial activity for healthier plants.',
                    npkRatio: '4-4-4',
                    coverage: 'Up to 250 sq. meters',
                    supplier: 'EcoFarm Supplies',
                    inventoryCount: 0,
                    inStock: false,
                    basePrice: 2800,
                    currency: 'Ksh',
                    minOrder: 1,
                    maxOrder: 20,
                    imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
                },
                {
                    name: 'Seed Starter Mix',
                    weight: '10 Kilograms',
                    type: 'Specialty',
                    description: 'Premium blend for starting seeds and cuttings. Provides perfect moisture retention and aeration for optimal germination and root development.',
                    npkRatio: '5-5-5',
                    coverage: 'Up to 100 sq. meters',
                    supplier: 'EcoFarm Supplies',
                    inventoryCount: 115,
                    inStock: true,
                    basePrice: 2500,
                    currency: 'Ksh',
                    minOrder: 1,
                    maxOrder: 40,
                    imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg',
                    promotion: {
                        discount: '25% OFF',
                        endDate: new Date('2025-05-01')
                    }
                },
                {
                    name: 'Potato & Root Crop Feed',
                    weight: '18 Kilograms',
                    type: 'Specialty',
                    description: 'Specially formulated for potatoes, carrots, beets, and other root vegetables. Promotes larger, healthier roots and tubers.',
                    npkRatio: '5-15-5',
                    coverage: 'Up to 180 sq. meters',
                    supplier: 'EcoFarm Supplies',
                    inventoryCount: 67,
                    inStock: true,
                    basePrice: 3750,
                    currency: 'Ksh',
                    minOrder: 1,
                    maxOrder: 30,
                    imageUrl: 'https://image.made-in-china.com/202f0j00SKpWwAoscucy/High-Quality-BOPP-Laminated-PP-Woven-Chemicals-Urea-Fertilizer-Bag-25kg-50kg-100kg.jpg'
                }
            ]
        }
    },
    computed: {
        allProducts() {
            return [...this.productsRow1, ...this.productsRow2, ...this.productsRow3];
        },
        filteredProductsRow1() {
            return this.filterProducts(this.productsRow1);
        },
        filteredProductsRow2() {
            return this.filterProducts(this.productsRow2);
        },
        filteredProductsRow3() {
            return this.filterProducts(this.productsRow3);
        }
    },
    methods: {
        handleSearch(query) {
            this.searchQuery = query;
            console.log('Search query:', query);
        },
        handleRegionChange(region) {
            this.currentRegion = region;
            console.log('Region changed to:', region);
        },
        handleFilterChange(filterType) {
            this.filterType = filterType;
            console.log('Filter changed to:', filterType);
        },
        applyAllFilters(filters) {
            this.searchQuery = filters.query;
            this.currentRegion = filters.region;
            this.filterType = filters.filter;
            console.log('Applied all filters:', filters);
        },
        filterProducts(products) {
            // First filter by search query if exists
            let filtered = [...products];

            if (this.searchQuery && this.searchQuery.trim() !== '') {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(product =>
                    product.name.toLowerCase().includes(query) ||
                    product.description.toLowerCase().includes(query) ||
                    product.type.toLowerCase().includes(query)
                );
            }

            // Then apply any sorting based on filter type
            switch (this.filterType) {
                case 'price_asc':
                    filtered.sort((a, b) => a.basePrice - b.basePrice);
                    break;

                case 'price_desc':
                    filtered.sort((a, b) => b.basePrice - a.basePrice);
                    break;

                case 'discount':
                    // Put products with promotions first
                    filtered.sort((a, b) => {
                        if (a.promotion && !b.promotion) return -1;
                        if (!a.promotion && b.promotion) return 1;

                        // If both have promotions, sort by discount percentage
                        if (a.promotion && b.promotion) {
                            const discountA = parseInt(a.promotion.discount);
                            const discountB = parseInt(b.promotion.discount);
                            return discountB - discountA;
                        }

                        return 0;
                    });
                    break;

                case 'new':
                    // For demo purposes, we'll just randomize to simulate "newest"
                    filtered.sort(() => Math.random() - 0.5);
                    break;
            }

            return filtered;
        }
    }
}
</script>

<style scoped>
.page-container {
    display: flex;
    justify-content: center;
    background-color: #f5f7fa;
    min-height: 100vh;
    padding: 40px 20px;
}

.content-container {
    width: 1200px;
    max-width: 100%;
}

.page-title {
    font-size: 1.8rem;
    color: #37474f;
    margin-bottom: 32px;
    font-weight: 600;
    text-align: left;
}

.location {
    font-weight: 800;
    color: #1b5e20;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.product-column {
    display: flex;
    justify-content: center;
}

/* Responsive adjustments */
@media (max-width: 960px) {
    .product-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 650px) {
    .product-grid {
        grid-template-columns: 1fr;
    }
}
</style>