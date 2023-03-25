import { defineStore } from 'pinia'
import { logUserIn } from '@/services/api'
import { useStorage } from '@vueuse/core'

export const useUserStore = defineStore('user', {
  state: () => ({
    token: useStorage('token', ''),
    error: '',
    loading: false
  }),
  getters: {
    isLoggedIn(state) {
      return state.token.length > 0;
    }
  },
  actions: {
    async login(username, password, onSuccess) {
      this.error = "";
      this.token = "";
      
      const me = this;
      logUserIn(
        username, 
        password, 
        data => {
          me.token = data.token
          onSuccess()
        },
        data => me.error = data.message
        )
    },
    logout() {
      this.token = "";
    },
    startLoading() {
      this.loading = true;
    },
    stopLoading() {
      this.loading = false;
    }
  }
});
