import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: () => ({
    token: '',
    error: '',
  }),
  actions: {
    async login(username, password) {
      this.error = "";
      this.token = "";
      
      const response = await fetch("http://localhost:8009/api/login",{
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          username: username.value, 
          password: password.value
        })
      });

      const me = this;
      if (response.status == 200) {
        response.json().then(data => me.token = data.token)
      } else {
        response.json().then(data => me.error = data.message)
      }
    }
  }
});
