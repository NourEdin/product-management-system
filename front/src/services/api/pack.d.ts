import { QueryParams } from './base'

declare function list(
    options: QueryParams,
    onSuccess: Function,
    onFailure?: Function,
): void

declare function remove(
    id: number,
    onSuccess: Function,
    onFailure?: Function,
): void

declare function get(
    id: number,
    onSuccess: Function,
    onFailure?: Function,
): void

declare function edit(
    id: number,
    pack: Pack,
    onSuccess: Function,
    onFailure?: Function,
): void

declare function add(
    pack: Pack,
    onSuccess: Function,
    onFailure?: Function,
): void

declare function batchUpdate (
    enabled: boolean,
    onSuccess: Function,
    onFailure?: Function
): void

interface Pack {
    name: string,
    productIds: Number[],
    enabled: boolean
}

export {list, remove, edit, add, get, batchUpdate}