import { call, QueryParams } from './base'


//Fetches the product list
const list = async (options: QueryParams, onSuccess: Function, onFailure:Function = null) => {
    call(
      'products',
      {
        method: 'GET',
        queryParams: options,
        onSuccess: response => onSuccess(response.data),
        onFailure: response => onFailure ? onFailure(response.error) : 0
      }
    )
  }
  //Calls the API to delete a product
  const remove = async (id: number, onSuccess: Function, onFailure: Function = null) => {
    call(
      `product/${id}`,
      {
        method: 'DELETE',
        onSuccess: response => onSuccess(response.data),
        onFailure: response => onFailure ? onFailure(response.error) : 0
      }
    )
  }
  //Calls the API to fetch a single product
  const get = async (id: number, onSuccess: Function, onFailure:Function = null) => {
    call(
      `product/${id}`,
      {
        method: 'GET',
        onSuccess: response => onSuccess(response.data),
        onFailure: response => onFailure ? onFailure(response.error) : 0
      }
    )
  }
  //Calls the API to edit a product
  const edit = async (id: number, product: Product, onSuccess: Function, onFailure: Function = null) => {
    call(
      `product/${id}`,
      {
        method: 'PUT',
        data: {
          name: product.name,
          number: product.number
        },
        onSuccess: response => onSuccess(response.data),
        onFailure: response => onFailure ? onFailure(response.error) : 0
      }
    )
  }
  
  //Calls the API to create a product
  const add = async (product: Product,onSuccess: Function, onFailure: Function = null) => {
    call(
      'product',
      {
        method: 'POST',
        data: {
          name: product.name,
          number: product.number
        },
        onSuccess: response => onSuccess(response.data),
        onFailure: response => onFailure ? onFailure(response.error) : 0
      }
    )
  }

interface Product {
  name: string;
  number: string;
}


export {
    list,
    remove,
    edit,
    add,
    get,
}