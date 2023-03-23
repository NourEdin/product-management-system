import { call } from './base'

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

export {
    login
}