import { call } from './base'


//Fetches the pack list
const list = async (options, onSuccess, onFailure = null) => {
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
  const remove = async (id, onSuccess, onFailure = null) => {
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
  const get = async (id, onSuccess, onFailure = null) => {
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
  const edit = async (id, pack, onSuccess, onFailure = null) => {
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
  const add = async (pack, onSuccess, onFailure = null) => {
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
  const batchUpdate = async (enabled, onSuccess, onFailure = null) => {
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

export {
    list,
    remove,
    edit,
    add,
    get,
    batchUpdate
}