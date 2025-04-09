import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}



export interface SharedData {
    name: string;
    csrf_token: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    totalCartPrice: number;
    totalCartQuantity:number;
    miniCartItems:cartItem[];
    success: string;
    error: string;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    roles: string[]; // Array of role names (e.g., ['admin', 'user'])
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}


export type Product = {
    id: number;
    title: string;
    slug: string;
    price: number;
    stock: number;
    image: string;
    images: Image[];
    description: string;
    short_description: string;
    created_by:{
        id: number;
        name: string;
    };
    department: {
        id: number;
        name: string;
    };
    category: {
        id: number;
        name: string;
    };
    variationTypes:VariationType[];
    variations:Array<{
        id: number;
        quantity: number;
        price: number;
        variation_type_option_ids: number[];
    }>
    vendor: {
        user_id: number;
        store_name: string;
        store_address: string;
        status: string;
    };
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
}

export interface PaginationLink {
    url: string;
    label: string;
    active: boolean;
}
export interface PaginationMetadata {
    current_page: number;
    last_page: number;
    from: number;
    to: number;
    total: number;
    per_page: number;
    count: number;
    links: PaginationLink[]
}


export type PaginationProps<T> = {
    data:Array<T>;
}

export type VariationType = {
    id: number;
    name: string;
    type: 'Select' | 'Radio' | 'Image';
    options:VariationTypeOption[];
}

export type VariationTypeOption = {
    id: number;
    name: string;
    images?: Image[];
    type:VariationType
}

export type Image = {
    id: number;
    thumb:string;
    small:string;
    large:string;
}

export type CartItem = {
    id:number;
    product_id:number;
    title:string;
    slug:string;
    price:number;
    quantity:number;
    image:string;
    option_ids:Record<string, number>;
    options:VariationTypeOption[]
}

export type GroupedCartItems = {
    user:User,
    items:CartItem[];
    totalQuantity:number;
    totalPrice:number;
}
