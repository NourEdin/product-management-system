import { useUserStore } from "../../stores/user";


const apiBase = import.meta.env.VITE_BACKEND_API_BASE;

//Makes the actual API call using fetch API
const call = async (endpoint, options) => {
  const userStore = useUserStore();

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
    fetchOptions.headers["Authorization"] = `Bearer ${userStore.token}`
  }
  //Append body data only if provided and is not a GET request
  if (options.data && options.method != 'GET') {
    fetchOptions.body = JSON.stringify(options.data)
  }
  //Append query parameters to the URL
  if(options.queryParams) {
    const query = Object.keys(options.queryParams).map(function(key) {
      return [key, options.queryParams[key]].map(encodeURIComponent).join("=");
    }).join("&");
    endpoint = `${endpoint}?${query}`
  }

  userStore.startLoading();

  const response = await fetch(`${apiBase}${endpoint}`, fetchOptions);

  userStore.stopLoading();

  if (response.status == 200) { //Call onSuccess
    if(options.onSuccess)
      response.json().then(options.onSuccess)
  } else { //Call onFailure
    if (options.onFailure)
      response.json().then(options.onFailure)
  }
}

export {
    call
}