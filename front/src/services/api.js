import { useUserStore } from "../stores/user";


const apiBase = import.meta.env.VITE_BACKEND_API_BASE;

//Makes the actual API call using fetch API
const call = async (endpoint, options) => {
  //Preparing fetch API options
  const fetchOptions = {
    method: options.method,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
  }
  //If noAuth is provided, skip adding the authorization header
  if (!options.noAuth) {
    const {token} = useUserStore()
    fetchOptions.headers["Authorization"] = `Bearer ${token}`
  }
  //Append body data only if provided and is not a GET request
  if (options.data && options.method != 'GET') {
    fetchOptions.body = JSON.stringify(options.data)
  }

  const response = await fetch(`${apiBase}${endpoint}`, fetchOptions);

  if (response.status == 200) { //Call onSuccess
    if(options.onSuccess)
      response.json().then(options.onSuccess)
  } else { //Call onFailure
    if (options.onFailure)
      response.json().then(options.onFailure)
  }
}

//Calls the login endpoint to log in using username and password
const login = async (username, password, onSuccess, onFailure) => {
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

//Fetches the product list
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
//Calls the API to delete a product
const deleteProduct = async (id, onSuccess, onFailure = null) => {
  call(
    `product/${id}`,
    {
      method: 'DELETE',
      onSuccess,
      onFailure
    }
  )
}
//Calls the API to fetch a single product
const getProduct = async (id, onSuccess, onFailure = null) => {
  call(
    `product/${id}`,
    {
      method: 'GET',
      onSuccess,
      onFailure
    }
  )
}
//Calls the API to edit a product
const editProduct = async (id, product, onSuccess, onFailure = null) => {
  call(
    `product/${id}`,
    {
      method: 'PUT',
      data: {
        name: product.name,
        number: product.number
      },
      onSuccess,
      onFailure
    }
  )
}

//Calls the API to create a product
const addProduct = async (product,onSuccess, onFailure = null) => {
  call(
    'product',
    {
      method: 'POST',
      data: {
        name: product.name,
        number: product.number
      },
      onSuccess,
      onFailure
    }
  )
}
export {
  login,
  getProducts,
  deleteProduct,
  getProduct,
  editProduct,
  addProduct,
}