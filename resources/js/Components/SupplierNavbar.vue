<template>
  <nav class="navbar">
    <div class="navbar-container">
      <!-- Logo Section -->
      <div class="logo-container">
        <Link :href="route('dashboard')">
        <svg width="120" height="40" viewBox="0 0 1065 287" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M47.5763 0.919822C34.183 3.34828 22.9973 13.0989 18.6188 26.1979C17.6989 28.8839 15.9327 37.6411 14.1298 48.2012C13.0259 54.7507 13.0995 57.1792 14.4977 59.9388C15.8591 62.6248 17.1102 63.8758 19.5754 65.0901C23.7332 67.1874 28.9213 66.5251 32.3064 63.4711C34.9925 61.0794 35.5076 59.6076 37.1634 50.3721C40.1438 33.4097 40.7325 31.4596 43.9336 28.1848C47.2084 24.8365 52.1389 23.1807 56.4439 23.9534C59.2035 24.4317 72.7808 29.2519 83.3409 33.4833C108.067 43.3443 132.756 55.8546 154.613 69.6158C157.814 71.6028 160.978 73.4793 161.677 73.7737C163.407 74.5096 167.601 74.4728 169.846 73.7001C172.421 72.817 175.549 69.6526 176.432 66.9666C177.941 62.5512 176.8 57.2896 173.746 54.346C171.317 52.0279 153.84 41.5414 140.005 34.072C127.053 27.0442 109.024 18.5814 94.7474 12.7678C81.354 7.32213 65.7162 1.8029 61.0064 0.919822C57.4741 0.257514 51.219 0.257514 47.5763 0.919822Z"
            fill="#2263AB" />
          <path
            d="M78.4097 71.198C73.6631 72.2283 68.3647 75.5398 65.605 79.1825C63.4341 82.0525 62.735 83.3771 61.5576 86.6518L60.5273 89.5954V118.295C60.5273 146.922 60.5273 146.995 61.3368 148.725C62.5143 151.3 64.7955 153.545 67.3712 154.722C73.148 157.298 80.3598 154.281 82.6779 148.357C83.2666 146.738 83.3402 144.714 83.4505 120.393L83.5609 94.158L91.803 98.4262L100.045 102.658H103.136C106.926 102.658 109.17 101.664 111.635 98.9413C113.659 96.6968 114.542 94.1948 114.505 90.7729C114.505 88.7123 114.285 87.8293 113.365 85.9159C112.702 84.5545 111.562 83.0091 110.716 82.2732C109.906 81.5741 105.049 78.9249 99.9714 76.3493C90.0368 71.382 87.8291 70.6461 83.1562 70.6829C81.8316 70.6829 79.6975 70.9404 78.4097 71.198Z"
            fill="#2263AB" />
          <path
            d="M18.2869 77.8211C14.5706 78.741 10.4128 81.2062 7.54282 84.0762C2.86987 88.786 0.772563 93.5693 0.441409 100.119C0.22064 104.976 0.919742 108.508 2.94346 112.519C5.15115 116.86 8.72026 120.724 12.2526 122.564L13.614 123.263V144.53C13.614 162.523 13.6876 166.092 14.1659 167.711C16.7783 176.505 28.6631 177.719 33.4096 169.698L34.4031 167.969L34.587 145.744L34.771 123.484L37.3467 121.754C42.13 118.516 45.8095 113.365 47.3181 107.809C48.3483 104.019 48.2012 97.7271 46.9869 94.0844C44.5953 86.7623 38.9656 80.8383 31.901 78.3362C28.9206 77.306 21.5249 77.0116 18.2869 77.8211ZM28.5527 91.2144C33.5568 93.4589 36.2428 98.9782 34.8078 104.24C33.4832 109.17 29.2518 112.592 24.5053 112.592C21.0833 112.592 18.9492 111.709 16.5208 109.318C14.1291 106.926 13.246 104.755 13.246 101.407C13.3196 93.2749 21.2305 88.0133 28.5527 91.2144Z"
            fill="#2263AB" />
          <path
            d="M201.452 81.611C196.705 83.929 194.167 88.8228 195.013 94.0108C195.675 98.0583 196.558 99.1989 204.101 105.528C214.22 114.027 220.99 120.209 222.204 122.049C223.897 124.587 224.633 127.089 224.633 130.438C224.633 137.245 222.977 139.379 209.142 150.491C186.624 168.594 132.167 206.125 104.497 222.646C99.3462 225.736 97.948 226.951 96.513 229.71C95.6299 231.366 95.5195 232.065 95.5195 234.935C95.5195 237.805 95.6299 238.504 96.513 240.16C97.7272 242.478 99.567 244.391 101.664 245.495C103.541 246.452 108.177 246.82 110.164 246.158C115.426 244.428 163.921 212.601 193.357 191.517C214.109 176.689 231.55 162.964 237.18 157.077C242.441 151.521 246.011 144.199 247.262 136.435C247.924 132.388 247.556 124.624 246.562 120.834C245.054 114.984 242.221 109.538 238.504 105.123C234.972 101.002 215.397 84.0026 211.681 81.7949C210.246 80.9854 209.436 80.7647 206.824 80.6543C203.917 80.5439 203.476 80.6175 201.452 81.611Z"
            fill="#2263AB" />
          <path
            d="M321.332 93.6797C318.977 94.5628 316.034 97.4328 314.856 100.045L313.899 102.216L313.973 149.461L314.083 196.669L315.187 198.655C316.585 201.158 318.94 203.255 321.59 204.248C324.202 205.242 328.286 205.095 330.935 203.917C333.364 202.85 336.124 199.906 337.043 197.368C337.816 195.344 337.816 195.234 337.742 148.357L337.632 101.37L336.786 99.7876C335.535 97.4696 333.364 95.2251 331.303 94.1948C329.059 93.0542 323.834 92.7966 321.332 93.6797Z"
            fill="#112E49" />
          <path
            d="M369.9 94.2316C367.435 95.4458 365.522 97.4328 364.234 100.045C363.535 101.443 363.388 102.363 363.388 105.233C363.388 109.465 364.124 111.341 366.773 113.99C368.797 115.977 370.416 116.86 373.175 117.376C374.389 117.596 402.979 117.744 452.321 117.744C538.568 117.744 530.841 117.486 533.895 120.43C535.992 122.453 536.876 124.367 536.876 127.053C536.876 130.585 535.22 133.529 532.166 135.295L530.436 136.325L459.422 136.509C394.627 136.693 388.224 136.766 386.384 137.318C382.374 138.533 380.129 139.526 377.37 141.329C372.071 144.788 368.833 148.504 366.184 154.244C363.424 160.205 363.388 160.389 363.388 179.191V195.933L364.344 197.956C365.485 200.385 367.803 202.776 370.121 203.917C372.586 205.131 377.701 205.131 380.166 203.917C382.889 202.593 384.766 200.716 386.09 198.03L387.304 195.565L387.488 181.583L387.672 167.601L388.666 165.908C389.696 164.179 391.425 162.633 393.265 161.824C394.075 161.456 409.271 161.309 463.654 161.161L533.012 160.978L536.14 159.91C547.804 155.973 555.715 147.989 559.21 136.619C560.093 133.639 560.203 132.866 560.203 127.126C560.203 121.349 560.13 120.614 559.173 117.56C557.996 113.696 555.641 109.097 553.286 106.006C548.944 100.413 541.843 95.777 534.741 93.974L531.246 93.091H451.695H372.145L369.9 94.2316Z"
            fill="#112E49" />
          <path
            d="M623.932 93.6797C609.656 96.7705 596.998 108.582 590.375 125.029C585.371 137.466 584.451 152.147 587.836 165.099C590.78 176.321 595.6 184.968 603.106 192.437C610.355 199.723 616.941 203.292 625.367 204.579C627.133 204.837 647.149 204.947 689.574 204.874L751.205 204.763L754.259 203.917C761.508 201.967 767.689 198.251 773.871 192.106C783.622 182.355 789.435 169.183 790.833 153.803C792.342 137.061 785.903 118.111 774.644 106.3C768.941 100.303 763.017 96.4761 755.989 94.3052L752.677 93.275L689.574 93.2014C635.044 93.1646 626.14 93.2014 623.932 93.6797ZM746.532 118.332C753.965 119.804 760.514 124.992 764.12 132.278C768.83 141.771 768.609 157.261 763.605 166.865C761.582 170.765 756.909 175.291 752.861 177.278C746.606 180.332 751.978 180.111 688.286 180.111C635.338 180.111 631.475 180.074 629.23 179.449C619.774 176.91 612.82 169.551 610.465 159.616C609.067 153.655 608.92 145.928 610.097 139.857C612.342 128.451 620.179 120.209 630.739 118.148C631.622 117.964 657.415 117.817 688.029 117.78C737.076 117.744 744.03 117.817 746.532 118.332Z"
            fill="#112E49" />
          <path
            d="M842.825 93.6429C839.256 94.4156 834.914 96.2553 831.787 98.3159C821.08 105.344 815.119 117.964 816.517 130.622C817.032 135.331 817.768 137.944 819.608 141.844C823.324 149.681 829.8 155.826 837.674 158.991C843.414 161.272 839.146 161.161 918.218 161.161C998.983 161.161 992.838 160.941 995.892 163.774C998.136 165.834 999.13 168.005 999.093 170.839C999.093 172.09 998.872 173.709 998.541 174.444C997.805 176.247 995.892 178.345 994.089 179.301L992.58 180.111L912.736 180.295L832.891 180.479L830.83 181.509C828.402 182.686 825.973 185.152 824.869 187.58C823.655 190.229 823.729 195.234 825.017 197.956C826.157 200.348 828.549 202.813 830.867 203.954L832.523 204.763H913.839H995.156L998.394 203.733C1007.92 200.753 1014.77 195.05 1019.07 186.55C1021.76 181.215 1022.75 176.211 1022.46 169.367C1022.24 163.664 1021.39 160.205 1019.07 155.532C1014.07 145.303 1004.32 138.275 993.132 136.914C991.035 136.619 966.53 136.509 918.623 136.509H847.204L845.291 135.552C840.47 133.161 838.447 127.494 840.765 122.968C841.574 121.386 844.003 119.068 845.511 118.369C846.284 118.038 865.123 117.891 926.166 117.744L1005.83 117.56L1007.89 116.603C1010.57 115.352 1012.82 113.071 1014.03 110.421C1014.84 108.729 1015.03 107.809 1015.03 105.233C1015.03 102.547 1014.88 101.811 1014.03 100.045C1012.82 97.58 1011.05 95.7034 1008.66 94.3052L1006.93 93.275L925.982 93.2014C860.303 93.1646 844.628 93.2382 842.825 93.6429Z"
            fill="#112E49" />
          <path
            d="M131.91 97.4696C128.414 98.3894 125.876 100.45 124.293 103.651C123.263 105.785 123.079 106.484 123.116 108.913C123.116 111.856 123.889 114.174 125.618 116.456C126.096 117.081 129.923 119.804 134.081 122.527C138.239 125.25 141.624 127.641 141.55 127.789C141.513 127.973 131.947 133.896 120.32 140.998C108.692 148.063 98.6105 154.391 97.9482 155.017C97.2859 155.679 96.2925 157.114 95.7405 158.218C94.8942 159.947 94.7471 160.72 94.7471 163.369C94.7471 166.055 94.8942 166.791 95.7773 168.594C96.9548 170.986 99.0889 173.046 101.517 174.187C102.805 174.776 103.909 174.96 106.153 174.923C108.692 174.923 109.391 174.776 111.158 173.819C112.298 173.23 122.711 166.938 134.338 159.874C153.398 148.246 155.716 146.738 158.218 144.236C163.038 139.453 164.988 134.669 164.988 127.678C164.988 122.858 164.437 120.54 162.339 116.456C160.168 112.261 157.041 109.465 147.916 103.578C143.574 100.781 139.453 98.2791 138.791 97.9847C137.282 97.3592 133.418 97.0648 131.91 97.4696Z"
            fill="#2263AB" />
          <path
            d="M66.3779 169.477C64.1702 170.25 61.9993 172.31 60.8219 174.702L59.7916 176.799L59.6077 210.283L59.4237 243.766L58.3566 245.716C57.7679 246.746 56.7745 248.034 56.149 248.512C54.4564 249.764 35.507 260.876 35.0287 260.876C34.6975 260.876 34.5872 253.149 34.5136 230.225L34.4032 199.612L33.4833 197.846C32.4531 195.822 30.8709 194.314 28.5896 193.099C26.4187 191.996 22.2241 191.959 19.8324 193.026C17.6983 194.019 15.785 195.896 14.6811 198.177L13.7981 199.98L13.6877 235.855C13.6141 261.611 13.6877 272.356 13.982 273.938C14.9387 278.831 18.2502 283.173 22.7392 285.271C24.7629 286.227 25.7564 286.448 28.332 286.595C30.0246 286.669 32.3059 286.522 33.3361 286.301C34.6975 285.97 39.5913 283.32 50.6297 276.845C59.1293 271.877 67.0402 267.094 68.2913 266.211C72.4123 263.267 76.055 258.631 78.2259 253.517C80.6175 247.961 80.5807 248.66 80.5807 210.945V176.799L79.6977 174.96C78.6306 172.715 76.5333 170.655 74.3256 169.661C72.3387 168.741 68.6592 168.668 66.3779 169.477Z"
            fill="#2263AB" />
        </svg>
        </Link>
      </div>

      <!-- Navigation Links - Hidden on mobile -->
      <div class="all-nav-links">
        <Link :href="route('dashboard')" class="nav-link disabled" :class="{ active: route().current('orders.*') }" @click.prevent>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <path opacity="0.4"
            d="M4 18.6458V8.05426C4 5.20025 4 3.77325 4.87868 2.88663C5.75736 2 7.17157 2 10 2H14C16.8284 2 18.2426 2 19.1213 2.88663C20 3.77325 20 5.20025 20 8.05426V18.6458C20 20.1575 20 20.9133 19.538 21.2108C18.7831 21.6971 17.6161 20.6774 17.0291 20.3073C16.5441 20.0014 16.3017 19.8485 16.0325 19.8397C15.7417 19.8301 15.4949 19.9768 14.9709 20.3073L13.06 21.5124C12.5445 21.8374 12.2868 22 12 22C11.7132 22 11.4555 21.8374 10.94 21.5124L9.02913 20.3073C8.54415 20.0014 8.30166 19.8485 8.03253 19.8397C7.74172 19.8301 7.49493 19.9768 6.97087 20.3073C6.38395 20.6774 5.21687 21.6971 4.46195 21.2108C4 20.9133 4 20.1575 4 18.6458Z"
            fill="currentColor" />
          <path
            d="M4 18.6458V8.05426C4 5.20025 4 3.77325 4.87868 2.88663C5.75736 2 7.17157 2 10 2H14C16.8284 2 18.2426 2 19.1213 2.88663C20 3.77325 20 5.20025 20 8.05426V18.6458C20 20.1575 20 20.9133 19.538 21.2108C18.7831 21.6971 17.6161 20.6774 17.0291 20.3073C16.5441 20.0014 16.3017 19.8485 16.0325 19.8397C15.7417 19.8301 15.4949 19.9768 14.9709 20.3073L13.06 21.5124C12.5445 21.8374 12.2868 22 12 22C11.7132 22 11.4555 21.8374 10.94 21.5124L9.02913 20.3073C8.54415 20.0014 8.30166 19.8485 8.03253 19.8397C7.74172 19.8301 7.49493 19.9768 6.97087 20.3073C6.38395 20.6774 5.21687 21.6971 4.46195 21.2108C4 20.9133 4 20.1575 4 18.6458Z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M16 6L8 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M10 10H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path
            d="M14.5 9.875C13.6716 9.875 13 10.4626 13 11.1875C13 11.9124 13.6716 12.5 14.5 12.5C15.3284 12.5 16 13.0876 16 13.8125C16 14.5374 15.3284 15.125 14.5 15.125M14.5 9.875C15.1531 9.875 15.7087 10.2402 15.9146 10.75M14.5 9.875V9M14.5 15.125C13.8469 15.125 13.2913 14.7598 13.0854 14.25M14.5 15.125V16"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
        </svg>
        Orders
        </Link>

        <Link :href="route('supplier.inventories.index')" class="nav-link"
          :class="{ active: route().current('supplier.inventories.*') }">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <path opacity="0.4"
            d="M5.5 16.092V15.2555C5.5 14.5602 5.5 14.2125 5.72475 14.0621C5.94951 13.9116 6.26431 14.0486 6.89392 14.3225L8.39392 14.975C8.68787 15.1029 8.83484 15.1668 8.91742 15.294C9 15.4211 9 15.5834 9 15.908V16.7445C9 17.4398 9 17.7875 8.77525 17.9379C8.55049 18.0884 8.23568 17.9514 7.60608 17.6775L6.10608 17.025C5.81213 16.8971 5.66516 16.8332 5.58258 16.706C5.5 16.5789 5.5 16.4166 5.5 16.092Z"
            fill="currentColor" />
          <path opacity="0.4"
            d="M13.6094 21.5422L17.8043 19.9313L17.8043 19.9313C19.9348 19.1132 21 18.7042 21 18V6.5L12 10V22C12.4173 22 12.8147 21.8474 13.6094 21.5422Z"
            fill="currentColor" />
          <path
            d="M17.8043 4.06866L13.6094 2.45779C12.8147 2.1526 12.4173 2 12 2C11.5827 2 11.1853 2.1526 10.3906 2.45779L6.19572 4.06866C4.06524 4.88678 3 5.29585 3 6C3 6.70415 4.06524 7.11322 6.19573 7.93134L10.3906 9.54221C11.1853 9.8474 11.5827 10 12 10C12.4173 10 12.8147 9.8474 13.6094 9.54221L17.8043 7.93134C19.9348 7.11322 21 6.70415 21 6C21 5.29585 19.9348 4.88678 17.8043 4.06866Z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path
            d="M21 6V18C21 18.7042 19.9348 19.1132 17.8043 19.9313L13.6094 21.5422C12.8147 21.8474 12.4173 22 12 22C11.5827 22 11.1853 21.8474 10.3906 21.5422L6.19573 19.9313C4.06524 19.1132 3 18.7042 3 18V6"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M12 10V22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M16.5 4L7 8V10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round" />
          <path
            d="M5.5 16.092V15.2555C5.5 14.5602 5.5 14.2125 5.72475 14.0621C5.94951 13.9116 6.26431 14.0486 6.89392 14.3225L8.39392 14.975C8.68787 15.1029 8.83484 15.1668 8.91742 15.294C9 15.4211 9 15.5834 9 15.908V16.7445C9 17.4398 9 17.7875 8.77525 17.9379C8.55049 18.0884 8.23568 17.9514 7.60608 17.6775L6.10608 17.025C5.81213 16.8971 5.66516 16.8332 5.58258 16.706C5.5 16.5789 5.5 16.4166 5.5 16.092Z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Inventory
        </Link>

        <Link :href="route('products.index')" class="nav-link" :class="{ active: route().current('products.*') }">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <path opacity="0.4"
            d="M10 22H16C17.8856 22 18.8284 22 19.4142 21.4142C20 20.8284 20 19.8856 20 18V16.5L17 15H15L13 13H10V22Z"
            fill="currentColor" />
          <path opacity="0.4"
            d="M8.82843 6.82843L7 5C7 3.7776 7.00039 2.76774 7.38076 2.38786C7.76152 2.00758 8.37435 2.00758 9.6 2.00758H15.1535C15.8599 2.00758 16.7765 1.89489 17.2487 2.53866C17.7502 3.22243 17.3216 4.1621 17.5388 4.91785C17.6049 5.14763 17.7681 5.32509 17.9184 5.50519L20 8H10H9.81994C9.66112 7.66112 9.37305 7.37305 8.82843 6.82843L8.82843 6.82843Z"
            fill="currentColor" />
          <path opacity="0.4"
            d="M5.17157 6.82843L7 5L8.82843 6.82843C9.40649 7.40649 9.69552 7.69552 9.84776 8.06306C10 8.4306 10 8.83935 10 9.65685V22H8C6.11438 22 5.17157 22 4.58579 21.4142C4 20.8284 4 19.8856 4 18V9.65685C4 8.83935 4 8.4306 4.15224 8.06306C4.30448 7.69552 4.59351 7.40649 5.17157 6.82843Z"
            fill="currentColor" />
          <path
            d="M5.17157 6.82843L7 5L8.82843 6.82843C9.40649 7.40649 9.69552 7.69552 9.84776 8.06306C10 8.4306 10 8.83935 10 9.65685V22H8C6.11438 22 5.17157 22 4.58579 21.4142C4 20.8284 4 19.8856 4 18V9.65685C4 8.83935 4 8.4306 4.15224 8.06306C4.30448 7.69552 4.59351 7.40649 5.17157 6.82843Z"
            stroke="currentColor" stroke-width="1.5" />
          <path
            d="M7 5.00379V4.60429C7 3.38019 7 2.76814 7.38076 2.38786C7.76152 2.00758 8.37435 2.00758 9.6 2.00758H15.1535C15.8599 2.00758 16.7765 1.89489 17.2487 2.53866C17.7502 3.22243 17.3216 4.1621 17.5388 4.91785C17.6049 5.14763 17.7681 5.32509 17.9184 5.50519L20 8"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
          <path d="M10 8H20V18C20 19.8856 20 20.8284 19.4142 21.4142C18.8284 22 17.8856 22 16 22H10"
            stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
          <path
            d="M10 13C10.4762 13.5 11.7434 13.4761 12.4869 13.2789C13.0054 13.1415 13.564 13.3027 13.9154 13.7639L14.6025 14.6658C14.9978 15.1846 15.5969 15.4309 16.1871 15.3173L17.0567 15.1499C17.5352 15.0578 18.011 15.2904 18.3367 15.7055C19.3522 17 20 17 20 17"
            stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
        </svg>
        Products
        </Link>

        <Link :href="route('supplierwarehouse.index')" class="nav-link"
          :class="{ active: route().current('supplierwarehouse.*') }">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <path opacity="0.4"
            d="M10.4478 2.98351L4.94777 5.2993C3.76103 5.79898 3.16767 6.04882 2.83383 6.5516C2.5 7.05438 2.5 7.6982 2.5 8.98585V21.5H5.5V11.5C5.5 10.5572 5.5 10.0858 5.79289 9.79289C6.08579 9.5 6.55719 9.5 7.5 9.5H16.5C17.4428 9.5 17.9142 9.5 18.2071 9.79289C18.5 10.0858 18.5 10.5572 18.5 11.5V21.5H21.5V8.98585C21.5 7.6982 21.5 7.05438 21.1662 6.5516C20.8323 6.04882 20.239 5.79898 19.0522 5.2993L13.5522 2.98351C12.7867 2.66117 12.4039 2.5 12 2.5C11.5961 2.5 11.2133 2.66117 10.4478 2.98351Z"
            fill="currentColor" />
          <path
            d="M10.4478 2.98351L4.94777 5.2993C3.76103 5.79898 3.16767 6.04882 2.83383 6.5516C2.5 7.05438 2.5 7.6982 2.5 8.98585V21.5H5.5V11.5C5.5 10.5572 5.5 10.0858 5.79289 9.79289C6.08579 9.5 6.55719 9.5 7.5 9.5H16.5C17.4428 9.5 17.9142 9.5 18.2071 9.79289C18.5 10.0858 18.5 10.5572 18.5 11.5V21.5H21.5V8.98585C21.5 7.6982 21.5 7.05438 21.1662 6.5516C20.8323 6.04882 20.239 5.79898 19.0522 5.2993L13.5522 2.98351C12.7867 2.66117 12.4039 2.5 12 2.5C11.5961 2.5 11.2133 2.66117 10.4478 2.98351Z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M11 6.5H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round" />
          <path
            d="M13 15.5H11C10.0572 15.5 9.58579 15.5 9.29289 15.7929C9 16.0858 9 16.5572 9 17.5V19.5C9 20.4428 9 20.9142 9.29289 21.2071C9.58579 21.5 10.0572 21.5 11 21.5H13C13.9428 21.5 14.4142 21.5 14.7071 21.2071C15 20.9142 15 20.4428 15 19.5V17.5C15 16.5572 15 16.0858 14.7071 15.7929C14.4142 15.5 13.9428 15.5 13 15.5Z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Warehouses
        </Link>

        <Link :href="route('supplier.users.index')" class="nav-link"
          :class="{ active: route().current('supplier.users.*') }">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <path opacity="0.4"
            d="M15 8C15 9.65685 13.6569 11 12 11C10.3431 11 9 9.65685 9 8C9 6.34315 10.3431 5 12 5C13.6569 5 15 6.34315 15 8Z"
            fill="currentColor" />
          <path opacity="0.4"
            d="M13.7143 14H10.2857C7.91876 14 5.99998 15.9188 5.99998 18.2857C5.99998 19.2325 6.76749 20 7.71426 20H16.2857C17.2325 20 18 19.2325 18 18.2857C18 15.9188 16.0812 14 13.7143 14Z"
            fill="currentColor" />
          <path
            d="M15 8C15 9.65685 13.6569 11 12 11C10.3431 11 9 9.65685 9 8C9 6.34315 10.3431 5 12 5C13.6569 5 15 6.34315 15 8Z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M16 4C17.6569 4 19 5.34315 19 7C19 8.22309 18.2681 9.27523 17.2183 9.7423" stroke="currentColor"
            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path
            d="M13.7143 14H10.2857C7.91876 14 5.99998 15.9188 5.99998 18.2857C5.99998 19.2325 6.76749 20 7.71426 20H16.2857C17.2325 20 18 19.2325 18 18.2857C18 15.9188 16.0812 14 13.7143 14Z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M17.7143 13C20.0812 13 22 14.9188 22 17.2857C22 18.2325 21.2325 19 20.2857 19" stroke="currentColor"
            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M8 4C6.34315 4 5 5.34315 5 7C5 8.22309 5.73193 9.27523 6.78168 9.7423" stroke="currentColor"
            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M3.71429 19C2.76751 19 2 18.2325 2 17.2857C2 14.9188 3.91878 13 6.28571 13" stroke="currentColor"
            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Users
        </Link>
      </div>

      <!-- User Profile Dropdown - Hidden on mobile -->
      <div class="nav-actions">
        <div class="profile-dropdown" @click="toggleDropdown" ref="dropdown">
          <div class="profile-button">
            <span class="username">{{ $page.props.auth.user.name }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000"
              fill="none">
              <path d="M5.99977 9.00005L11.9998 15L17.9998 9" stroke="currentColor" stroke-width="1.5"
                stroke-miterlimit="16" />
            </svg>
          </div>
          <div class="dropdown-menu" v-show="isDropdownOpen">
            <Link :href="route('supplier.details')" class="dropdown-item">Company Details</Link>
            <Link :href="route('logout')" method="post" class="dropdown-item">Sign Out</Link>
          </div>
        </div>

        <!-- Mobile Menu Toggle Button -->
        <button class="mobile-menu-toggle" @click="toggleMobileMenu">
          <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </button>
      </div>
    </div>

    <!-- Mobile Menu Drawer -->
    <div class="mobile-menu-drawer" :class="{ 'open': isMobileMenuOpen }">
      <div class="mobile-menu-header">
        <div class="mobile-menu-logo">
          <Link :href="route('dashboard')">
          <!-- Logo SVG -->
          </Link>
        </div>
        <button class="mobile-menu-close" @click="toggleMobileMenu">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="mobile-menu-content">
        <Link :href="route('dashboard')" class="mobile-nav-link disabled" :class="{ active: route().current('orders.*') }" @click.prevent>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <!-- Orders icon SVG -->
        </svg>
        <span>Orders</span>
        </Link>

        <Link :href="route('supplier.inventories.index')" class="mobile-nav-link"
          :class="{ active: route().current('supplier.inventories.*') }" @click="closeMobileMenu">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <!-- Inventory icon SVG -->
        </svg>
        <span>Inventory</span>
        </Link>

        <Link :href="route('products.index')" class="mobile-nav-link" :class="{ active: route().current('products.*') }"
          @click="closeMobileMenu">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <!-- Products icon SVG -->
        </svg>
        <span>Products</span>
        </Link>

        <Link :href="route('supplierwarehouse.index')" class="mobile-nav-link"
          :class="{ active: route().current('supplierwarehouse.*') }" @click="closeMobileMenu">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <!-- Warehouses icon SVG -->
        </svg>
        <span>Warehouses</span>
        </Link>

        <Link :href="route('supplier.users.index')" class="mobile-nav-link"
          :class="{ active: route().current('supplier.users.*') }" @click="closeMobileMenu">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <!-- Users icon SVG -->
        </svg>
        <span>Users</span>
        </Link>

        <Link :href="route('supplier.details')" class="mobile-nav-link" @click="closeMobileMenu">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <!-- Company Details icon SVG -->
        </svg>
        <span>Company Details</span>
        </Link>

        <Link :href="route('logout')" method="post" class="mobile-nav-link" @click="closeMobileMenu">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none"
          class="nav-icon">
          <!-- Sign Out icon SVG -->
        </svg>
        <span>Sign Out</span>
        </Link>
      </div>
    </div>

    <!-- Overlay for mobile menu -->
    <div v-if="isMobileMenuOpen" class="mobile-menu-overlay" @click="toggleMobileMenu"></div>
  </nav>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  name: 'SupplierNavbar',
  components: {
    Link
  },
  data() {
    return {
      isDropdownOpen: false,
      isMobileMenuOpen: false,
      clickOutsideHandler: null
    }
  },
  methods: {
    toggleDropdown() {
      this.isDropdownOpen = !this.isDropdownOpen
    },
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;

      if (this.isMobileMenuOpen) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    },
    closeMobileMenu() {
      this.isMobileMenuOpen = false;
      document.body.style.overflow = '';
    }
  },
  mounted() {
    this.clickOutsideHandler = (e) => {
      if (this.$refs.dropdown && !this.$refs.dropdown.contains(e.target)) {
        this.isDropdownOpen = false
      }
    }

    document.addEventListener('click', this.clickOutsideHandler)
  },
  beforeUnmount() {
    if (this.clickOutsideHandler) {
      document.removeEventListener('click', this.clickOutsideHandler)
    }
  }
}
</script>

<style scoped>
.navbar {
  width: 100%;
  height: 72px;
  background-color: #ffffff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.navbar-container {
  max-width: 1200px;
  width: 100%;
  height: 100%;
  margin: 0 auto;
  display: flex;
  align-items: center;
  box-sizing: border-box;
  position: relative;
  padding: 0 24px;
}

.logo-container {
  display: flex;
  align-items: center;
  margin-right: 24px;
  z-index: 10;
}

.all-nav-links {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-grow: 1;
}

.nav-link {
  position: relative;
  font-size: 16px;
  font-weight: 500;
  color: #4b5563;
  text-decoration: none;
  padding: 8px 4px;
  transition: color 0.2s ease-in-out;
  display: flex;
  align-items: center;
}

.nav-icon {
  width: 1.25rem;
  height: 1.25rem;
  margin-right: 8px;
}

.nav-link:hover {
  color: #2263AB;
}

.nav-link.active {
  color: #2263AB;
  font-weight: 600;
}

.nav-link.disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

.nav-actions {
  display: flex;
  align-items: center;
  margin-left: auto;
  gap: 16px;
  z-index: 10;
}

.profile-dropdown {
  position: relative;
}

.profile-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  cursor: pointer;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.profile-button:hover {
  background-color: #f5f5f5;
}

.username {
  font-weight: 500;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: #ffffff;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  min-width: 150px;
  margin-top: 0.5rem;
}

.dropdown-item {
  display: block;
  width: 100%;
  padding: 0.75rem 1rem;
  text-align: left;
  border: none;
  background: none;
  cursor: pointer;
  transition: background-color 0.2s;
  text-decoration: none;
  color: #4b5563;
}

.dropdown-item:hover {
  background-color: #f5f5f5;
}

/* Mobile styles */
.mobile-menu-toggle {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
}

.hamburger {
  width: 24px;
  height: 18px;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.hamburger span {
  display: block;
  height: 2px;
  width: 100%;
  background-color: #4b5563;
  border-radius: 2px;
}

.mobile-menu-drawer {
  position: fixed;
  top: -100vh;
  left: 0;
  width: 100%;
  height: auto;
  background-color: #ffffff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  z-index: 2000;
  transition: top 0.3s ease;
  overflow-y: auto;
}

.mobile-menu-drawer.open {
  top: 0;
}

.mobile-menu-header {
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  border-bottom: 1px solid #E5E7EB;
}

.mobile-menu-close {
  background: none;
  border: none;
  cursor: pointer;
  color: #4b5563;
  transition: color 0.2s ease;
  padding: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.mobile-menu-content {
  padding: 10px 24px;
  display: flex;
  flex-direction: column;
}

.mobile-nav-link {
  font-size: 18px;
  font-weight: 500;
  color: #4b5563;
  text-decoration: none;
  padding: 16px 0;
  display: flex;
  align-items: center;
  transition: color 0.2s ease;
  border-bottom: 1px solid #f3f4f6;
}

.mobile-nav-link .nav-icon {
  margin-right: 12px;
}

.mobile-nav-link:hover {
  color: #2263AB;
}

.mobile-nav-link.active {
  color: #2263AB;
  font-weight: 600;
}

.mobile-nav-link.disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

.mobile-menu-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1900;
  opacity: 0;
  animation: fadeIn 0.3s forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

@media (max-width: 768px) {
  .navbar-container {
    justify-content: space-between;
  }

  .all-nav-links {
    display: none;
  }

  .profile-dropdown {
    display: none;
  }

  .mobile-menu-toggle {
    display: block;
  }
}
</style>
