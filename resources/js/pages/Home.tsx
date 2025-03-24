import ProductItem from '@/components/app/product-item';
import CurrencyFormatter from '@/components/ui/currency-formatter';
import AppHeaderLayout from '@/layouts/app/app-header-layout';
import { Product, PaginationProps } from '@/types';


import { Head } from '@inertiajs/react';


export default function Home({ products }: PaginationProps<Product>) {
  console.log('products list: ', products);
  return (
    <AppHeaderLayout>
      <Head title="Home">
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
      </Head>
      <div className="hero bg-gray-200 h-[520px]">
        <div className="hero-content text-center">
          <div className="max-w-md">
            <h1 className="text-5xl font-bold">Hello there</h1>
            <p className="py-6">
              Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem
              quasi. In deleniti eaque aut repudiandae et a id nisi.
            </p>
            <button className="btn btn-primary">Get Started</button>
          </div>
        </div>
      </div>
      <div className="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3 p-8">
        {products.data.map((product: Product) => (
          <ProductItem key={product.id} product={product} />
        ))}
      </div>
    </AppHeaderLayout>
  );
}
