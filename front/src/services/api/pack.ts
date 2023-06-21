import { call, QueryParams } from './base'


//Fetches the pack list
const list = async (options: QueryParams, onSuccess: Function, onFailure: Function|null = null) => {
    call(
      'packs',
      {
        method: 'GET',
        queryParams: options,
        onSuccess: response => onSuccess(response.data),
        onFailure: response => onFailure ? onFailure(response.error) : 0
      }
    )
  }
  //Calls the API to delete a pack
  const remove = async (id: Number, onSuccess: Function, onFailure: Function|null = null) => {
    call(
      `pack/${id}`,
      {
        method: 'DELETE',
        onSuccess: response => onSuccess(response.data),
        onFailure: response => onFailure ? onFailure(response.error) : 0
      }
    )
  }
  //Calls the API to fetch a single pack
  const get = async (id: Number, onSuccess: Function, onFailure: Function|null = null) => {
    call(
      `pack/${id}`,
      {
        method: 'GET',
        onSuccess: response => onSuccess(response.data),
        onFailure: response => onFailure ? onFailure(response.error) : 0
      }
    )
  }
  //Calls the API to edit a pack
  const edit = async (id: Number, pack: Pack, onSuccess: Function, onFailure: Function|null = null) => {
    call(
      `pack/${id}`,
      {
        method: 'PUT',
        data: {
          name: pack.name,
          productIds: pack.productIds,
          enabled: pack.enabled
        },
        onSuccess: response => onSuccess(response.data),
        onFailure: response => onFailure ? onFailure(response.error) : 0
      }
    )
  }
  
  //Calls the API to create a pack
  const add = async (pack: Pack, onSuccess: Function, onFailure: Function|null = null) => {
    call(
      'pack',
      {
        method: 'POST',
        data: {
          name: pack.name,
          productIds: pack.productIds,
          enabled: pack.enabled
        },
        onSuccess: response => onSuccess(response.data),
        onFailure: response => onFailure ? onFailure(response.error) : 0
      }
    )
  }

  //Calls the API to batch update a pack
  const batchUpdate = async (enabled: boolean, onSuccess: Function, onFailure: Function|null = null) => {
    call(
      `packs`,
      {
        method: 'PUT',
        data: {
          enabled
        },
        onSuccess: response => onSuccess(response.data),
        onFailure: response => onFailure ? onFailure(response.error) : 0
      }
    )
  }

interface Pack {
  name: string,
  productIds: Number[],
  enabled: boolean
}

export {
    list,
    remove,
    edit,
    add,
    get,
    batchUpdate
}