<template>
    <div class="error-message" v-if="show">
      <div class="error-content">
        <div class="error-icon">
          <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.61205 1.01917C3.70288 0.817318 3.90365 0.6875 4.125 0.6875H10.5C10.7006 0.6875 10.8859 0.79424 10.9865 0.967662C11.0872 1.14109 11.0879 1.35499 10.9884 1.52908L8.4693 5.9375H11.25C11.4775 5.9375 11.6826 6.07455 11.7697 6.28474C11.8568 6.49493 11.8086 6.73685 11.6477 6.89773L3.39775 15.1477C3.21897 15.3265 2.94287 15.364 2.72286 15.2395C2.50284 15.115 2.39297 14.8588 2.4543 14.6136L3.77957 9.3125H0.750002C0.559135 9.3125 0.38128 9.21568 0.277645 9.0554C0.174002 8.89513 0.158717 8.69323 0.237047 8.51915L3.61205 1.01917Z" fill="#D9100A"/>
          </svg>
        </div>
        <div class="error-text">
          <slot>Error message comes here</slot>
        </div>
      </div>
      <button class="close-button" @click="closeError">
        <span class="close-icon">&times;</span>
      </button>
    </div>
  </template>
  
  <script>
  export default {
    name: 'ErrorMessage',
    props: {
      message: {
        type: String,
        default: 'Error message comes here'
      },
      initialShow: {
        type: Boolean,
        default: true
      }
    },
    data() {
      return {
        show: this.initialShow
      }
    },
    methods: {
      closeError() {
        this.show = false
        this.$emit('close')
      }
    }
  }
  </script>
  
  <style scoped>
  .error-message {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #FFEBEA;
    border: 1px solid #F4C3C2;
    border-radius: 4px;
    padding: 8px 12px;
    margin: 8px 0;
    color: #D9100A;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    font-size: 14px;
  }
  
  .error-content {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .error-icon {
    display: flex;
    align-items: center;
  }
  
  .error-text {
    font-weight: 500;
  }
  
  .close-button {
    background: transparent;
    border: none;
    color: #D9100A;
    font-size: 18px;
    cursor: pointer;
    padding: 0;
    margin-left: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 20px;
    width: 20px;
  }
  
  .close-icon {
    line-height: 1;
    position: relative;
    top: -2px;
  }
  </style>