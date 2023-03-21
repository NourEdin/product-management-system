import { useUserStore } from "../stores/user";


const apiBase = import.meta.env.VITE_BACKEND_API_BASE;

const call = async (endpoint, options) => {
    const fetchOptions = {
      method: options.method,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    }
    if (!options.noAuth) {
      const {token} = useUserStore()
      fetchOptions.headers["Authorization"] = `Bearer ${token}`
    }
    if (options.data) {
      fetchOptions.body = JSON.stringify(options.data)
    }

    const response = await fetch(`${apiBase}${endpoint}`, fetchOptions);

    if (response.status == 200) {
      if(options.onSuccess)
        response.json().then(options.onSuccess)
    } else {
      if (options.onFailure)
        response.json().then(options.onFailure)
    }
}
const login = async (username, password, onSuccess, onFailure) => {
  console.log("Loggin user in ", username, password)
  call(
    'login',
    {
      method: 'POST',
      data: {
        username,
        password
      },
      onSuccess,
      onFailure
    }
  )
}

const getProducts = async (onSuccess, onFailure = null) => {
  call(
    'products',
    {
      method: 'GET',
      onSuccess,
      onFailure
    }
  )
}

export {
  login,
  getProducts
}