<script setup>
import InputError from "@/Components/InputError.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const submit = () => {
  form.post(route("login"), {
    onFinish: () => form.reset("password"),
    onSuccess: (response) => {
      const user = response?.props?.auth?.user;
      
      if (user.user_type === "admin") {
        window.location.href = route("admin.suppliers.index");
      } else if (user.user_type === "supplier") {
        window.location.href = route("supplier.inventories.index");
      } else {
        window.location.href = route("dashboard");
      }
    },
  });
};

const showPassword = ref(false);
const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};
</script>

<template>
  <Head title="Log in" />

  <div class="signup-container">
    <div class="signup-card">
      <div class="logo-container">
        <svg
          width="120"
          height="40"
          viewBox="0 0 1065 287"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M47.5763 0.919822C34.183 3.34828 22.9973 13.0989 18.6188 26.1979C17.6989 28.8839 15.9327 37.6411 14.1298 48.2012C13.0259 54.7507 13.0995 57.1792 14.4977 59.9388C15.8591 62.6248 17.1102 63.8758 19.5754 65.0901C23.7332 67.1874 28.9213 66.5251 32.3064 63.4711C34.9925 61.0794 35.5076 59.6076 37.1634 50.3721C40.1438 33.4097 40.7325 31.4596 43.9336 28.1848C47.2084 24.8365 52.1389 23.1807 56.4439 23.9534C59.2035 24.4317 72.7808 29.2519 83.3409 33.4833C108.067 43.3443 132.756 55.8546 154.613 69.6158C157.814 71.6028 160.978 73.4793 161.677 73.7737C163.407 74.5096 167.601 74.4728 169.846 73.7001C172.421 72.817 175.549 69.6526 176.432 66.9666C177.941 62.5512 176.8 57.2896 173.746 54.346C171.317 52.0279 153.84 41.5414 140.005 34.072C127.053 27.0442 109.024 18.5814 94.7474 12.7678C81.354 7.32213 65.7162 1.8029 61.0064 0.919822C57.4741 0.257514 51.219 0.257514 47.5763 0.919822Z"
            fill="#2263AB"
          />
          <path
            d="M78.4097 71.198C73.6631 72.2283 68.3647 75.5398 65.605 79.1825C63.4341 82.0525 62.735 83.3771 61.5576 86.6518L60.5273 89.5954V118.295C60.5273 146.922 60.5273 146.995 61.3368 148.725C62.5143 151.3 64.7955 153.545 67.3712 154.722C73.148 157.298 80.3598 154.281 82.6779 148.357C83.2666 146.738 83.3402 144.714 83.4505 120.393L83.5609 94.158L91.803 98.4262L100.045 102.658H103.136C106.926 102.658 109.17 101.664 111.635 98.9413C113.659 96.6968 114.542 94.1948 114.505 90.7729C114.505 88.7123 114.285 87.8293 113.365 85.9159C112.702 84.5545 111.562 83.0091 110.716 82.2732C109.906 81.5741 105.049 78.9249 99.9714 76.3493C90.0368 71.382 87.8291 70.6461 83.1562 70.6829C81.8316 70.6829 79.6975 70.9404 78.4097 71.198Z"
            fill="#2263AB"
          />
          <path
            d="M18.2869 77.8211C14.5706 78.741 10.4128 81.2062 7.54282 84.0762C2.86987 88.786 0.772563 93.5693 0.441409 100.119C0.22064 104.976 0.919742 108.508 2.94346 112.519C5.15115 116.86 8.72026 120.724 12.2526 122.564L13.614 123.263V144.53C13.614 162.523 13.6876 166.092 14.1659 167.711C16.7783 176.505 28.6631 177.719 33.4096 169.698L34.4031 167.969L34.587 145.744L34.771 123.484L37.3467 121.754C42.13 118.516 45.8095 113.365 47.3181 107.809C48.3483 104.019 48.2012 97.7271 46.9869 94.0844C44.5953 86.7623 38.9656 80.8383 31.901 78.3362C28.9206 77.306 21.5249 77.0116 18.2869 77.8211ZM28.5527 91.2144C33.5568 93.4589 36.2428 98.9782 34.8078 104.24C33.4832 109.17 29.2518 112.592 24.5053 112.592C21.0833 112.592 18.9492 111.709 16.5208 109.318C14.1291 106.926 13.246 104.755 13.246 101.407C13.3196 93.2749 21.2305 88.0133 28.5527 91.2144Z"
            fill="#2263AB"
          />
          <path
            d="M201.452 81.611C196.705 83.929 194.167 88.8228 195.013 94.0108C195.675 98.0583 196.558 99.1989 204.101 105.528C214.22 114.027 220.99 120.209 222.204 122.049C223.897 124.587 224.633 127.089 224.633 130.438C224.633 137.245 222.977 139.379 209.142 150.491C186.624 168.594 132.167 206.125 104.497 222.646C99.3462 225.736 97.948 226.951 96.513 229.71C95.6299 231.366 95.5195 232.065 95.5195 234.935C95.5195 237.805 95.6299 238.504 96.513 240.16C97.7272 242.478 99.567 244.391 101.664 245.495C103.541 246.452 108.177 246.82 110.164 246.158C115.426 244.428 163.921 212.601 193.357 191.517C214.109 176.689 231.55 162.964 237.18 157.077C242.441 151.521 246.011 144.199 247.262 136.435C247.924 132.388 247.556 124.624 246.562 120.834C245.054 114.984 242.221 109.538 238.504 105.123C234.972 101.002 215.397 84.0026 211.681 81.7949C210.246 80.9854 209.436 80.7647 206.824 80.6543C203.917 80.5439 203.476 80.6175 201.452 81.611Z"
            fill="#2263AB"
          />
          <path
            d="M321.332 93.6797C318.977 94.5628 316.034 97.4328 314.856 100.045L313.899 102.216L313.973 149.461L314.083 196.669L315.187 198.655C316.585 201.158 318.94 203.255 321.59 204.248C324.202 205.242 328.286 205.095 330.935 203.917C333.364 202.85 336.124 199.906 337.043 197.368C337.816 195.344 337.816 195.234 337.742 148.357L337.632 101.37L336.786 99.7876C335.535 97.4696 333.364 95.2251 331.303 94.1948C329.059 93.0542 323.834 92.7966 321.332 93.6797Z"
            fill="#112E49"
          />
          <path
            d="M369.9 94.2316C367.435 95.4458 365.522 97.4328 364.234 100.045C363.535 101.443 363.388 102.363 363.388 105.233C363.388 109.465 364.124 111.341 366.773 113.99C368.797 115.977 370.416 116.86 373.175 117.376C374.389 117.596 402.979 117.744 452.321 117.744C538.568 117.744 530.841 117.486 533.895 120.43C535.992 122.453 536.876 124.367 536.876 127.053C536.876 130.585 535.22 133.529 532.166 135.295L530.436 136.325L459.422 136.509C394.627 136.693 388.224 136.766 386.384 137.318C382.374 138.533 380.129 139.526 377.37 141.329C372.071 144.788 368.833 148.504 366.184 154.244C363.424 160.205 363.388 160.389 363.388 179.191V195.933L364.344 197.956C365.485 200.385 367.803 202.776 370.121 203.917C372.586 205.131 377.701 205.131 380.166 203.917C382.889 202.593 384.766 200.716 386.09 198.03L387.304 195.565L387.488 181.583L387.672 167.601L388.666 165.908C389.696 164.179 391.425 162.633 393.265 161.824C394.075 161.456 409.271 161.309 463.654 161.161L533.012 160.978L536.14 159.91C547.804 155.973 555.715 147.989 559.21 136.619C560.093 133.639 560.203 132.866 560.203 127.126C560.203 121.349 560.13 120.614 559.173 117.56C557.996 113.696 555.641 109.097 553.286 106.006C548.944 100.413 541.843 95.777 534.741 93.974L531.246 93.091H451.695H372.145L369.9 94.2316Z"
            fill="#112E49"
          />
          <path
            d="M623.932 93.6797C609.656 96.7705 596.998 108.582 590.375 125.029C585.371 137.466 584.451 152.147 587.836 165.099C590.78 176.321 595.6 184.968 603.106 192.437C610.355 199.723 616.941 203.292 625.367 204.579C627.133 204.837 647.149 204.947 689.574 204.874L751.205 204.763L754.259 203.917C761.508 201.967 767.689 198.251 773.871 192.106C783.622 182.355 789.435 169.183 790.833 153.803C792.342 137.061 785.903 118.111 774.644 106.3C768.941 100.303 763.017 96.4761 755.989 94.3052L752.677 93.275L689.574 93.2014C635.044 93.1646 626.14 93.2014 623.932 93.6797ZM746.532 118.332C753.965 119.804 760.514 124.992 764.12 132.278C768.83 141.771 768.609 157.261 763.605 166.865C761.582 170.765 756.909 175.291 752.861 177.278C746.606 180.332 751.978 180.111 688.286 180.111C635.338 180.111 631.475 180.074 629.23 179.449C619.774 176.91 612.82 169.551 610.465 159.616C609.067 153.655 608.92 145.928 610.097 139.857C612.342 128.451 620.179 120.209 630.739 118.148C631.622 117.964 657.415 117.817 688.029 117.78C737.076 117.744 744.03 117.817 746.532 118.332Z"
            fill="#112E49"
          />
          <path
            d="M842.825 93.6429C839.256 94.4156 834.914 96.2553 831.787 98.3159C821.08 105.344 815.119 117.964 816.517 130.622C817.032 135.331 817.768 137.944 819.608 141.844C823.324 149.681 829.8 155.826 837.674 158.991C843.414 161.272 839.146 161.161 918.218 161.161C998.983 161.161 992.838 160.941 995.892 163.774C998.136 165.834 999.13 168.005 999.093 170.839C999.093 172.09 998.872 173.709 998.541 174.444C997.805 176.247 995.892 178.345 994.089 179.301L992.58 180.111L912.736 180.295L832.891 180.479L830.83 181.509C828.402 182.686 825.973 185.152 824.869 187.58C823.655 190.229 823.729 195.234 825.017 197.956C826.157 200.348 828.549 202.813 830.867 203.954L832.523 204.763H913.839H995.156L998.394 203.733C1007.92 200.753 1014.77 195.05 1019.07 186.55C1021.76 181.215 1022.75 176.211 1022.46 169.367C1022.24 163.664 1021.39 160.205 1019.07 155.532C1014.07 145.303 1004.32 138.275 993.132 136.914C991.035 136.619 966.53 136.509 918.623 136.509H847.204L845.291 135.552C840.47 133.161 838.447 127.494 840.765 122.968C841.574 121.386 844.003 119.068 845.511 118.369C846.284 118.038 865.123 117.891 926.166 117.744L1005.83 117.56L1007.89 116.603C1010.57 115.352 1012.82 113.071 1014.03 110.421C1014.84 108.729 1015.03 107.809 1015.03 105.233C1015.03 102.547 1014.88 101.811 1014.03 100.045C1012.82 97.58 1011.05 95.7034 1008.66 94.3052L1006.93 93.275L925.982 93.2014C860.303 93.1646 844.628 93.2382 842.825 93.6429Z"
            fill="#112E49"
          />
          <path
            d="M131.91 97.4696C128.414 98.3894 125.876 100.45 124.293 103.651C123.263 105.785 123.079 106.484 123.116 108.913C123.116 111.856 123.889 114.174 125.618 116.456C126.096 117.081 129.923 119.804 134.081 122.527C138.239 125.25 141.624 127.641 141.55 127.789C141.513 127.973 131.947 133.896 120.32 140.998C108.692 148.063 98.6105 154.391 97.9482 155.017C97.2859 155.679 96.2925 157.114 95.7405 158.218C94.8942 159.947 94.7471 160.72 94.7471 163.369C94.7471 166.055 94.8942 166.791 95.7773 168.594C96.9548 170.986 99.0889 173.046 101.517 174.187C102.805 174.776 103.909 174.96 106.153 174.923C108.692 174.923 109.391 174.776 111.158 173.819C112.298 173.23 122.711 166.938 134.338 159.874C153.398 148.246 155.716 146.738 158.218 144.236C163.038 139.453 164.988 134.669 164.988 127.678C164.988 122.858 164.437 120.54 162.339 116.456C160.168 112.261 157.041 109.465 147.916 103.578C143.574 100.781 139.453 98.2791 138.791 97.9847C137.282 97.3592 133.418 97.0648 131.91 97.4696Z"
            fill="#2263AB"
          />
          <path
            d="M66.3779 169.477C64.1702 170.25 61.9993 172.31 60.8219 174.702L59.7916 176.799L59.6077 210.283L59.4237 243.766L58.3566 245.716C57.7679 246.746 56.7745 248.034 56.149 248.512C54.4564 249.764 35.507 260.876 35.0287 260.876C34.6975 260.876 34.5872 253.149 34.5136 230.225L34.4032 199.612L33.4833 197.846C32.4531 195.822 30.8709 194.314 28.5896 193.099C26.4187 191.996 22.2241 191.959 19.8324 193.026C17.6983 194.019 15.785 195.896 14.6811 198.177L13.7981 199.98L13.6877 235.855C13.6141 261.611 13.6877 272.356 13.982 273.938C14.9387 278.831 18.2502 283.173 22.7392 285.271C24.7629 286.227 25.7564 286.448 28.332 286.595C30.0246 286.669 32.3059 286.522 33.3361 286.301C34.6975 285.97 39.5913 283.32 50.6297 276.845C59.1293 271.877 67.0402 267.094 68.2913 266.211C72.4123 263.267 76.055 258.631 78.2259 253.517C80.6175 247.961 80.5807 248.66 80.5807 210.945V176.799L79.6977 174.96C78.6306 172.715 76.5333 170.655 74.3256 169.661C72.3387 168.741 68.6592 168.668 66.3779 169.477Z"
            fill="#2263AB"
          />
        </svg>
      </div>
      <h1>Sign In</h1>
      <p class="subtitle">Welcome back! Please enter your details</p>

      <form @submit.prevent="submit" class="signup-form">
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
          {{ status }}
        </div>

        <div class="form-group">
          <input
            id="email"
            type="email"
            class="form-input"
            v-model="form.email"
            required
            autofocus
            autocomplete="username"
            placeholder="Email Address"
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="form-group password-group">
          <div class="password-input-container">
            <input
              id="password"
              :type="showPassword ? 'text' : 'password'"
              class="form-input"
              v-model="form.password"
              required
              autocomplete="current-password"
              placeholder="Password"
            />
            <button
              type="button"
              class="password-toggle-btn"
              @click="togglePasswordVisibility"
            >
              <!-- Eye Close Icon (shown when password is hidden) -->
              <svg
                v-if="!showPassword"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                width="24"
                height="24"
                color="#000000"
                fill="none"
              >
                <path
                  d="M19.439 15.439C21 14 22 12 22 12C20.5 9 16.6892 5 12 5C11.0922 5 10.2294 5.15476 9.41827 5.41827M17 17.4186C15.5657 18.3368 13.8793 19 12 19C7.31078 19 3.5 15 2 12C2 12 3.5 9 6.5 6.91847"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M9.85786 10C9.32783 10.53 9 11.2623 9 12.0711C9 13.6887 10.3113 15 11.9289 15C12.7377 15 13.47 14.6722 14 14.1421"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M3 3L21 21"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              <!-- Eye Open Icon (shown when password is visible) -->
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                width="24"
                height="24"
                color="#000000"
                fill="none"
              >
                <path
                  d="M15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12Z"
                  stroke="currentColor"
                  stroke-width="1.5"
                />
                <path
                  d="M12 5C17.5228 5 22 12 22 12C22 12 17.5228 19 12 19C6.47715 19 2 12 2 12C2 12 6.47715 5 12 5Z"
                  stroke="currentColor"
                  stroke-width="1.5"
                />
              </svg>
            </button>
          </div>
          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <button
          type="submit"
          class="signup-button"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Sign In
        </button>

        <p class="login-text" v-if="canResetPassword">
          Forgot your password?
          <Link :href="route('password.request')" class="login-link"
            >Reset</Link
          >
        </p>
      </form>
    </div>
  </div>
</template>

<style scoped>
.signup-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: #f8f9fa;
  padding: 10px;
  box-sizing: border-box;
  overflow: hidden;
}

.signup-card {
  background: white;
  padding: 48px;
  border-radius: 16px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 500px;
}

.logo-container {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

h1 {
  color: #2d3748;
  font-size: 34px;
  margin-bottom: 12px;
  text-align: center;
  font-weight: bold;
}

.subtitle {
  color: #718096;
  text-align: center;
  margin-bottom: 24px;
  font-size: 16px;
}

.form-group {
  margin-bottom: 12px;
  position: relative;
}

.form-input {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 17px;
  outline: none;
  transition: border-color 0.2s;
  box-sizing: border-box;
}

.form-input:focus {
  border-color: #4299e1;
}

.password-group {
  position: relative;
}

.password-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input-container input {
  width: 100%;
  padding-right: 40px;
}

.password-toggle-btn {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  padding: 0;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
}

.password-toggle-btn:hover {
  color: #0e64a5;
}

.password-toggle-btn svg {
  display: block;
}

.mt-4 {
  margin-top: 10px;
  margin-bottom: 10px;
}

.block {
  display: block;
  margin-bottom: 16px;
}

:deep(.form-checkbox) {
  width: 16px;
  height: 16px;
  border-radius: 4px;
  border: 1px solid #e2e8f0;
}

.signup-button {
  width: 100%;
  padding: 12px;
  background-color: #0e64a5;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 17px;
  cursor: pointer;
  margin-top: 10px;
  margin-bottom: 24px;
  transition: opacity 0.2s;
}

.signup-button:hover:not(:disabled) {
  opacity: 0.9;
}

.signup-button:disabled {
  cursor: not-allowed;
}

.login-text {
  text-align: center;
  color: #718096;
  margin-bottom: 0;
  font-size: 15px;
}

.login-link {
  color: #4299e1;
  text-decoration: none;
  font-weight: 500;
}

.login-link:hover {
  color: #2b6cb0;
}

.items-center {
  display: flex;
  align-items: center;
}

.ms-2 {
  margin-left: 8px;
  font-size: 15px;
  color: #64748b;
}

:deep(.mt-2) {
  margin-top: 4px;
  font-size: 12px;
  color: #e53e3e;
}
</style>
