<template>
    <RetailerNavbar />
    <div class="page-container">
        <div class="content-container">
            <!-- Newly added Product Search Component -->
            <ProductSearchBar @search="handleSearch" @region-change="handleRegionChange"
                @filter-change="handleFilterChange" @apply-filters="applyAllFilters" />

            <h1 class="page-title">
                All Products in: <span class="location">{{ currentRegion }}</span>
            </h1>

            <div v-if="isLoading" class="loading-state">
                Loading products...
            </div>

            <div v-else-if="!hasAnyProducts" class="no-products">
                <i class="fas fa-info-circle"></i>
                Currently, there are no products available to purchase from the selected region. Please try selecting a different region.
            </div>

            <div v-else class="product-grid">
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
import ProductCard from "./Components/ProductCard.vue";
import ProductSearchBar from "./Components/ProductSearchBar.vue";
import RetailerNavbar from "./Components/RetailerNavbar.vue";
import axios from "axios";

export default {
    name: "ProductListingPage",
    components: {
        ProductCard,
        ProductSearchBar,
        RetailerNavbar,
    },
    data() {
        return {
            currentRegion: "Meru",
            searchQuery: "",
            filterType: "new",
            productsRow1: [],
            productsRow2: [],
            productsRow3: [],
            lastId: null,
            isLoading: false,
            hasMoreProducts: true,
            retailerCountry: "Kenya",
        };
    },
    computed: {
        hasAnyProducts() {
            return this.productsRow1.length > 0 || 
                   this.productsRow2.length > 0 || 
                   this.productsRow3.length > 0;
        },
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
        },
    },
    methods: {
        handleSearch(query) {
            this.searchQuery = query;
            console.log("Search query:", query);
        },
        handleRegionChange(region) {
            this.currentRegion = region;
            console.log("Region changed to:", region);
        },
        handleFilterChange(filterType) {
            this.filterType = filterType;
            console.log("Filter changed to:", filterType);
        },
        applyAllFilters(filters) {
            this.searchQuery = filters.query;
            this.currentRegion = filters.region;
            this.filterType = filters.filter;
            console.log("Applied all filters:", filters);
        },
        filterProducts(products) {
            // First filter by search query if exists
            let filtered = [...products];

            if (this.searchQuery && this.searchQuery.trim() !== "") {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(
                    (product) =>
                        product.name.toLowerCase().includes(query) ||
                        product.description.toLowerCase().includes(query) ||
                        product.type.toLowerCase().includes(query)
                );
            }

            // Then apply any sorting based on filter type
            switch (this.filterType) {
                case "price_asc":
                    filtered.sort((a, b) => a.basePrice - b.basePrice);
                    break;

                case "price_desc":
                    filtered.sort((a, b) => b.basePrice - a.basePrice);
                    break;

                case "discount":
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

                case "new":
                    // For demo purposes, we'll just randomize to simulate "newest"
                    filtered.sort(() => Math.random() - 0.5);
                    break;
            }

            return filtered;
        },
        async fetchProducts(loadMore = false) {
            if (this.isLoading || (!loadMore && !this.hasMoreProducts)) return;

            try {
                this.isLoading = true;
                const params = new URLSearchParams({
                    // search: this.searchQuery || "",
                    // region: this.currentRegion || "",
                    // category: this.filterType || "",
                    search: "",
                    region: "",
                    category: "",
                    manufacturer: "",
                    limit: 20,
                    lastId: loadMore ? this.lastId : "",
                });

                const response = await axios.get(`/retailers/product-search?${params.toString()}`);

                if (response.data) {
                    const newProducts = response.data.map((product) => ({
                        ...product,
                        inStock: product.stock_quantity > 0,
                    }));

                    if (!loadMore) {
                        this.productsRow1 = [];
                        this.productsRow2 = [];
                        this.productsRow3 = [];
                    }

                    if (newProducts.length > 0) {
                        this.lastId = newProducts[newProducts.length - 1].id;
                        this.hasMoreProducts = newProducts.length === 20;

                        const allCurrentProducts = [...this.productsRow1, ...this.productsRow2, ...this.productsRow3];
                        const allProducts = [...allCurrentProducts, ...newProducts];
                        const chunkSize = Math.ceil(allProducts.length / 3);

                        this.productsRow1 = allProducts.slice(0, chunkSize);
                        this.productsRow2 = allProducts.slice(chunkSize, chunkSize * 2);
                        this.productsRow3 = allProducts.slice(chunkSize * 2);
                    } else {
                        this.hasMoreProducts = false;
                    }
                }
            } catch (error) {
                console.error("Error fetching products:", error);
            } finally {
                this.isLoading = false;
            }
        },
    },
    mounted() {
        this.fetchProducts();
    },
};
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

.loading-state {
    text-align: center;
    padding: 40px;
    font-size: 1.2rem;
    color: #666;
}

.no-products {
    text-align: left;
    padding: 16px;
    font-size: 0.875rem;
    color: #C95B00;
    background: #fff8f5;
    border: 1px solid #ffdcc7;
    border-radius: 4px;
    margin: 20px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: bold;
}

.no-products i {
    color: #C95B00;
    font-size: 1rem;
}
</style>