import { Product } from "@/types";
import { Link } from "@inertiajs/react";
import CurrencyFormatter from "@/components/ui/currency-formatter";


export default function ProductItem({ product }: { product: Product }) {
  console.log('Product :', product);

  return (
    <div className="card bg-base-100 w-96 shadow-sm">
      <Link href={route('product.show', product.slug)} >
        <figure>
          <img
            src={product.image || "https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"}
            alt="Shoes"
            className="aspect-square object-cover" />
        </figure>
      </Link>
      <div className="card-body">
        <h2 className="card-title">{product.title}</h2>
        <p>A card component has a figure, a body part, and inside body there are title and actions parts</p>
        <div className="card-actions justify-between my-2">
          <div className="text-lg font-bold">
            <CurrencyFormatter amount={product.price} local="en-US" currency="PKR" />
          </div>
          <button className="btn btn-primary">Buy Now
          </button>
        </div>
      </div>
    </div>
  );
};
