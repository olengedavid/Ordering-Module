<!-- No changes to the template section -->
<template>
    <div class="search-container">
        <!-- Search input with updated SVG -->
        <div class="search-input-container">
            <div class="search-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" color="#000000"
                    fill="none">
                    <path opacity="0.4" fill-rule="evenodd" clip-rule="evenodd"
                        d="M16.7929 16.7929C17.1834 16.4024 17.8166 16.4024 18.2071 16.7929L22.7071 21.2929C23.0976 21.6834 23.0976 22.3166 22.7071 22.7071C22.3166 23.0976 21.6834 23.0976 21.2929 22.7071L16.7929 18.2071C16.4024 17.8166 16.4024 17.1834 16.7929 16.7929Z"
                        fill="currentColor" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11ZM11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3Z"
                        fill="currentColor" />
                </svg>
            </div>
            <input type="text" class="search-input" placeholder="Search for a product" v-model="searchQuery"
                @keyup.enter="handleSearch" />
        </div>

        <!-- Updated Region dropdown with multi-select -->
        <div class="dropdown-container region-dropdown">
            <div class="dropdown-trigger" @click="toggleRegionDropdown">
                <div class="dropdown-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" color="#000000"
                        fill="none">
                        <path opacity="0.4"
                            d="M7 18C5.17107 18.4117 4 19.0443 4 19.7537C4 20.9943 7.58172 22 12 22C16.4183 22 20 20.9943 20 19.7537C20 19.0443 18.8289 18.4117 17 18"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <path
                            d="M14.5 9C14.5 10.3807 13.3807 11.5 12 11.5C10.6193 11.5 9.5 10.3807 9.5 9C9.5 7.61929 10.6193 6.5 12 6.5C13.3807 6.5 14.5 7.61929 14.5 9Z"
                            stroke="currentColor" stroke-width="1.5" />
                        <path
                            d="M13.2574 17.4936C12.9201 17.8184 12.4693 18 12.0002 18C11.531 18 11.0802 17.8184 10.7429 17.4936C7.6543 14.5008 3.51519 11.1575 5.53371 6.30373C6.6251 3.67932 9.24494 2 12.0002 2C14.7554 2 17.3752 3.67933 18.4666 6.30373C20.4826 11.1514 16.3536 14.5111 13.2574 17.4936Z"
                            stroke="currentColor" stroke-width="1.5" />
                    </svg>
                </div>
                <span class="dropdown-label">
                    {{ getRegionSummary() }}
                </span>
                <div class="dropdown-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
            </div>

            <div class="dropdown-menu region-dropdown-menu" v-if="isRegionDropdownOpen">
                <div class="section-divider first-section">Region</div>
                <div class="select-all-container">
                    <button class="select-all-btn" @click="selectAllRegions">Select All</button>
                    <button class="select-all-btn" @click="unselectAllRegions">Unselect All</button>
                </div>
                <div class="dropdown-item checkbox-item" v-for="region in regions" :key="region"
                    @click="toggleRegion(region)">
                    {{ region }}
                    <span class="checkbox" :class="{ 'checked': isRegionSelected(region) }">
                        <svg v-if="isRegionSelected(region)" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                </div>

                <div class="filter-actions">
                    <button class="clear-filters-btn" @click="clearRegions">Clear All</button>
                    <button class="apply-filters-btn" @click="applyRegionFilters">Apply</button>
                </div>
            </div>
        </div>

        <!-- Updated Filter dropdown with only Category and Manufacturer sections -->
        <div class="dropdown-container filter-dropdown">
            <div class="dropdown-trigger" @click="toggleFilterDropdown">
                <span class="dropdown-label">
                    Filter by {{ getFilterSummary() }}
                </span>
                <div class="dropdown-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
            </div>

            <div class="dropdown-menu filter-dropdown-menu" v-if="isFilterDropdownOpen">
                <!-- Category section with Select All/Unselect All -->
                <div class="section-divider first-section">Category</div>
                <div class="select-all-container">
                    <button class="select-all-btn" @click="selectAllCategories">Select All</button>
                    <button class="select-all-btn" @click="unselectAllCategories">Unselect All</button>
                </div>
                <div class="dropdown-item checkbox-item" v-for="category in categories" :key="'cat-' + category.id"
                    @click="toggleCategory(category)">
                    {{ category.name }}
                    <span class="checkbox" :class="{ 'checked': isCategorySelected(category.id) }">
                        <svg v-if="isCategorySelected(category.id)" xmlns="http://www.w3.org/2000/svg" width="16"
                            height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                </div>

                <!-- Manufacturer section with Select All/Unselect All -->
                <div class="section-divider">Manufacturer</div>
                <div class="select-all-container">
                    <button class="select-all-btn" @click="selectAllManufacturers">Select All</button>
                    <button class="select-all-btn" @click="unselectAllManufacturers">Unselect All</button>
                </div>
                <div class="dropdown-item checkbox-item" v-for="manufacturer in manufacturers"
                    :key="'man-' + manufacturer.id" @click="toggleManufacturer(manufacturer)">
                    {{ manufacturer.name }}
                    <span class="checkbox" :class="{ 'checked': isManufacturerSelected(manufacturer.id) }">
                        <svg v-if="isManufacturerSelected(manufacturer.id)" xmlns="http://www.w3.org/2000/svg"
                            width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                </div>

                <!-- Apply and Clear buttons -->
                <div class="filter-actions">
                    <button class="clear-filters-btn" @click="clearFilters">Clear All</button>
                    <button class="apply-filters-btn" @click="applyFilters">Apply Filters</button>
                </div>
            </div>
        </div>

        <!-- Filter button -->
        <button class="filter-button" @click="applyAllFilters">
            Filter
        </button>
    </div>
</template>

<script>
export default {
    name: 'ProductSearchBar',
    data() {
        return {
            searchQuery: '',
            isRegionDropdownOpen: false,
            isFilterDropdownOpen: false,
            selectedRegions: ['Meru'], // Default region
            selectedCategories: [],
            selectedManufacturers: [],
            regions: ['Meru', 'Nairobi', 'Nakuru', 'Mombasa', 'Kisumu', 'Eldoret'],
            categories: [
                { id: 1, name: 'Fertilizer' },
                { id: 2, name: 'Lawn Care' },
                { id: 3, name: 'Organic' },
                { id: 4, name: 'Specialty' },
                { id: 5, name: 'Pest Control' }
            ],
            manufacturers: [
                { id: 1, name: 'Aden Agri Supplies' },
                { id: 2, name: 'Green Growth Ltd' },
                { id: 3, name: 'EcoFarm Supplies' },
                { id: 4, name: 'FarmTech Solutions' },
                { id: 5, name: 'Nature\'s Best' }
            ]
        }
    },
    methods: {
        toggleRegionDropdown() {
            this.isRegionDropdownOpen = !this.isRegionDropdownOpen;
            if (this.isRegionDropdownOpen) {
                this.isFilterDropdownOpen = false;
            }
        },
        toggleFilterDropdown() {
            this.isFilterDropdownOpen = !this.isFilterDropdownOpen;
            if (this.isFilterDropdownOpen) {
                this.isRegionDropdownOpen = false;
            }
        },
        toggleRegion(region) {
            const index = this.selectedRegions.indexOf(region);
            if (index === -1) {
                this.selectedRegions.push(region);
            } else {
                // Don't remove if it's the last selected region
                if (this.selectedRegions.length > 1) {
                    this.selectedRegions.splice(index, 1);
                }
            }
        },
        isRegionSelected(region) {
            return this.selectedRegions.includes(region);
        },
        // New methods for selecting all regions
        selectAllRegions() {
            this.selectedRegions = [...this.regions];
        },
        unselectAllRegions() {
            // Keep at least one region selected (Meru as default)
            this.selectedRegions = ['Meru'];
        },
        clearRegions() {
            // Keep at least one region
            this.selectedRegions = ['Meru'];
        },
        applyRegionFilters() {
            this.isRegionDropdownOpen = false;
            this.$emit('region-change', this.selectedRegions);
        },
        toggleCategory(category) {
            const index = this.selectedCategories.indexOf(category.id);
            if (index === -1) {
                this.selectedCategories.push(category.id);
            } else {
                this.selectedCategories.splice(index, 1);
            }
        },
        toggleManufacturer(manufacturer) {
            const index = this.selectedManufacturers.indexOf(manufacturer.id);
            if (index === -1) {
                this.selectedManufacturers.push(manufacturer.id);
            } else {
                this.selectedManufacturers.splice(index, 1);
            }
        },
        isCategorySelected(categoryId) {
            return this.selectedCategories.includes(categoryId);
        },
        isManufacturerSelected(manufacturerId) {
            return this.selectedManufacturers.includes(manufacturerId);
        },
        // New methods for selecting all categories
        selectAllCategories() {
            this.selectedCategories = this.categories.map(category => category.id);
        },
        unselectAllCategories() {
            this.selectedCategories = [];
        },
        // New methods for selecting all manufacturers
        selectAllManufacturers() {
            this.selectedManufacturers = this.manufacturers.map(manufacturer => manufacturer.id);
        },
        unselectAllManufacturers() {
            this.selectedManufacturers = [];
        },
        handleSearch() {
            this.$emit('search', this.searchQuery);
        },
        clearFilters() {
            this.selectedCategories = [];
            this.selectedManufacturers = [];
        },
        applyFilters() {
            this.isFilterDropdownOpen = false;
            this.$emit('filter-change', {
                categories: this.selectedCategories,
                manufacturers: this.selectedManufacturers
            });
        },
        applyAllFilters() {
            this.isRegionDropdownOpen = false;
            this.isFilterDropdownOpen = false;
            this.$emit('apply-filters', {
                query: this.searchQuery,
                regions: this.selectedRegions,
                categories: this.selectedCategories,
                manufacturers: this.selectedManufacturers
            });
        },
        getRegionSummary() {
            if (this.selectedRegions.length === 0) {
                return 'Select Region';
            } else if (this.selectedRegions.length === 1) {
                return this.selectedRegions[0];
            } else {
                return `${this.selectedRegions.length} regions`;
            }
        },
        getFilterSummary() {
            let parts = [];
            if (this.selectedCategories.length > 0) {
                parts.push(`${this.selectedCategories.length} categories`);
            }
            if (this.selectedManufacturers.length > 0) {
                parts.push(`${this.selectedManufacturers.length} manufacturers`);
            }

            return parts.length > 0 ? `(${parts.join(', ')})` : '';
        },
        // Close dropdowns when clicking outside
        closeDropdowns(event) {
            const target = event.target;
            if (!this.$el.contains(target)) {
                this.isRegionDropdownOpen = false;
                this.isFilterDropdownOpen = false;
            }
        }
    },
    mounted() {
        document.addEventListener('click', this.closeDropdowns);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.closeDropdowns);
    }
}
</script>

<style scoped>
.search-container {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    margin-bottom: 24px;
}

.search-input-container {
    position: relative;
    flex: 1;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9e9e9e;
    pointer-events: none;
}

.search-input {
    width: 100%;
    padding: 13px 16px 13px 44px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 16px;
    color: #424242;
    background-color: #ffffff;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    box-sizing: border-box;
}

.search-input:focus {
    border-color: #2196f3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.search-input::placeholder {
    color: #9e9e9e;
}

.dropdown-container {
    position: relative;
    min-width: 180px;
}

.region-dropdown {
    min-width: 140px;
}

.dropdown-trigger {
    display: flex;
    align-items: center;
    padding: 13px 16px;
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.dropdown-trigger:hover {
    border-color: #bdbdbd;
}

.dropdown-icon {
    margin-right: 8px;
    color: #424242;
}

.dropdown-label {
    flex: 1;
    color: #424242;
    font-size: 16px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: left;
}

.dropdown-arrow {
    margin-left: 8px;
    color: #9e9e9e;
    transition: transform 0.2s;
}

.dropdown-container:has(.dropdown-menu) .dropdown-arrow {
    transform: rotate(180deg);
}

.dropdown-menu {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    width: 100%;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    z-index: 10;
    max-height: 300px;
    overflow-y: auto;
}

.region-dropdown-menu {
    width: 240px;
}

.filter-dropdown-menu {
    width: 280px;
    right: 0;
    left: auto;
    max-height: 400px;
}

.dropdown-item {
    padding: 12px 16px;
    cursor: pointer;
    transition: background-color 0.2s;
    color: #424242;
    text-align: left;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dropdown-item:hover {
    background-color: #f5f5f5;
}

.dropdown-item.active {
    background-color: #e3f2fd;
    color: #1976d2;
    font-weight: 500;
}

.section-divider {
    padding: 12px 16px 8px;
    color: #757575;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    background-color: #f5f7fa;
    border-top: 1px solid #e0e0e0;
    border-bottom: 1px solid #e0e0e0;
    letter-spacing: 0.5px;
    text-align: left;
}

.first-section {
    border-top: none;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

.checkbox-item {
    padding-right: 12px;
}

.checkbox {
    width: 20px;
    height: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.checkbox.checked {
    border-color: #1976d2;
    background-color: #1976d2;
    color: white;
}

.filter-actions {
    display: flex;
    justify-content: space-between;
    padding: 12px 16px;
    background-color: #f5f7fa;
    border-top: 1px solid #e0e0e0;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}

.clear-filters-btn,
.apply-filters-btn {
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.clear-filters-btn {
    background-color: transparent;
    color: #757575;
    border: 1px solid #e0e0e0;
}

.clear-filters-btn:hover {
    background-color: #f5f5f5;
}

.apply-filters-btn {
    background-color: #1976d2;
    color: white;
    border: none;
}

.apply-filters-btn:hover {
    background-color: #1565c0;
}

.filter-button {
    padding: 13px 24px;
    background-color: #0E64A5;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.2s, transform 0.1s;
    white-space: nowrap;
}

.filter-button:hover {
    background-color: #0A5089;
}

.filter-button:active {
    transform: translateY(1px);
}

/* Select All container styles */
.select-all-container {
    display: flex;
    justify-content: space-between;
    padding: 8px 16px;
    background-color: #f9f9f9;
    border-bottom: 1px solid #eeeeee;
}

.select-all-btn {
    padding: 4px 8px;
    background-color: transparent;
    color: #1976d2;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.select-all-btn:hover {
    background-color: #e3f2fd;
    border-color: #1976d2;
}

/* Responsive adjustments - Updated to stack region and filter dropdowns */
@media (max-width: 768px) {
    .search-container {
        flex-direction: column;
        gap: 12px;
    }

    .search-input-container,
    .region-dropdown,
    .filter-dropdown,
    .filter-button {
        width: 100%;
        min-width: 100%;
    }

    .dropdown-menu {
        position: relative;
        top: 8px;
        width: 100%;
        left: 0;
        right: 0;
    }

    .region-dropdown-menu,
    .filter-dropdown-menu {
        width: 100%;
        max-width: none;
    }
}
</style>