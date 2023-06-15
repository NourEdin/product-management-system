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
    product: Product,
    onSuccess: Function,
    onFailure?: Function,
): void

declare function add(
    product: Product,
    onSuccess: Function,
    onFailure?: Function,
): void

interface Product {
    name: string;
    number: string;
}

export {list, remove, edit, add, get}