<template>
    <div class="success-message" v-if="show">
      <div class="success-content">
        <div class="success-icon">
          <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.25 8.5C0.25 13.0564 3.94365 16.75 8.5 16.75C13.0564 16.75 16.75 13.0564 16.75 8.5C16.75 3.94365 13.0564 0.25 8.5 0.25C3.94365 0.25 0.25 3.94365 0.25 8.5ZM12.0883 5.63195C12.4008 5.91836 12.4219 6.40382 12.1355 6.71626L7.9146 11.3209C7.77308 11.4752 7.57477 11.565 7.36549 11.5696C7.15621 11.5741 6.95419 11.493 6.80617 11.345L4.88757 9.42638C4.58787 9.12669 4.58787 8.64075 4.88757 8.34106C5.18728 8.04138 5.67319 8.04138 5.9729 8.34106L7.32474 9.69291L11.004 5.6791C11.2904 5.36666 11.7759 5.34555 12.0883 5.63195Z" fill="#1F8722"/>
          </svg>
        </div>
        <div class="success-text">
          <slot>Success message here</slot>
        </div>
      </div>
      <button class="close-button" @click="closeMessage">
        <span class="close-icon">&times;</span>
      </button>
    </div>
  </template>
  
  <script>
  export default {
    name: 'SuccessMessage',
    props: {
      message: {
        type: String,
        default: 'Success message here'
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
      closeMessage() {
        this.show = false
        this.$emit('close')
      }
    }
  }
  </script>
  
  <style scoped>
  .success-message {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #E6F4E6;
    border: 1px solid #C3E6C3;
    border-radius: 4px;
    padding: 8px 12px;
    margin: 8px 0;
    color: #1F8722;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    font-size: 14px;
  }
  
  .success-content {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .success-icon {
    display: flex;
    align-items: center;
  }
  
  .success-text {
    font-weight: 500;
  }
  
  .close-button {
    background: transparent;
    border: none;
    color: #1F8722;
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