import { Product } from "@/types";
import { Link, useForm } from "@inertiajs/react";
import CurrencyFormatter from "@/components/ui/currency-formatter";


export default function ProductItem({ product }: { product: Product }) {
  console.log('Product :', product);

  const form = useForm<{
    quantity: number;
    option_ids: Record<string, number>;
  }>({
    quantity: 1,
    option_ids: {},
  });

  const addToCart = () => {
    // console.log('Add to cart');
    form.post(route('cart.store', product.id), {
      preserveScroll: true,
      preserveState: true,
      onError: (err) => {
        console.log('Error :', err);
      },
      onSuccess: () => {
        alert('Item added successfully.');
      }
    })
  }

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
            <CurrencyFormatter amount={product.price} />
          </div>
          <button className="btn btn-primary"
            onClick={addToCart}>
            Add to cart
          </button>
        </div>
      </div>
    </div >
  );
};
