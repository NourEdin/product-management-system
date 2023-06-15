import { useUserStore } from "@/stores/user";


const apiBase = import.meta.env.VITE_BACKEND_API_BASE;

//Makes the actual API call using fetch API
const call = async (endpoint: string, options: ApiOptions) => {
  const userStore = useUserStore();

  //Preparing fetch API options
  const fetchOptions: FetchOptions = {
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
    const query = Object.keys(options.queryParams).map(function(key: keyof QueryParams) {
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
  } else if(response.status == 401) { //Token expired, you need to logout
    userStore.logout()
  }
  else { //Call onFailure
    if (options.onFailure)
      response.json().then(options.onFailure)
  }
}

//Calls the login endpoint to log in using username and password
const login = async (username: string, password: string, onSuccess: Function, onFailure: Function) => {
  call(
    'login',
    {
      method: 'POST',
      data: {
        username,
        password
      },
      onSuccess: response => onSuccess(response),
      onFailure: response => onFailure ? onFailure(response) : 0
    }
  )
}



interface ApiOptions {
  method?: string;
  noAuth?: boolean;
  data?: object;
  queryParams?: QueryParams;
  onSuccess?: (data: ResponseObject) => void;
  onFailure?: (reason: ResponseObject) => void;
}

interface ResponseObject {
  data? : object;
  error?: string;
}

interface FetchOptions {
  method: string;
  headers: {
    Accept: string;
    "Content-Type": string;
    Authorization?: string;
  };
  body?: string;
}

export interface QueryParams{
  term?: string;
  sort?: string;
  order?: 'desc'|'asc';
  max?: number;
  offset?: number;
}

interface ApiError {
  message: string;
}
export {
    call,
    login,
}