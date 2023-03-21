import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { login } from '@/services/api'

export const useUserStore = defineStore('user', {
  state: () => ({
    token: '',
    error: '',
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
      login(
        username, 
        password, 
        data => {
          me.token = data.token
          onSuccess()
        },
        data => me.error = data.message
        )
    }
  }
});
