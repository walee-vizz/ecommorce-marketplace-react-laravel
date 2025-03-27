import { GroupedCartItems } from '@/types';
import AppHeaderLayout from "@/layouts/app/app-header-layout";
import { Head, Link } from "@inertiajs/react";
import { CreditCardIcon } from 'lucide-react';
import CurrencyFormatter from '@/components/ui/currency-formatter';
import CartItem from '@/components/app/cart-item';
import { Button } from '@/components/ui/button';

export default function Index(
  {
    csrf_token,
    cartItems,
    totalCartPrice,
    totalCartQuantity
  }: {
    csrf_token: string;
    cartItems: Record<number, GroupedCartItems>;
    totalCartPrice: number;
    totalCartQuantity: number;
  }
) {

  console.log('items: ', cartItems);
  return (
    <AppHeaderLayout>
      <Head title="Your Cart" />
      <div className="container mx-auto p-8 flex flex-col lg:flex-row gap-4">
        <div className="card flex-1 bg-white dark:bg-gray-800 order-2 lg:order-1">
          <div className="card-body">
            <h2 className="text-lg font-bold">
              Shopping Cart
            </h2>
            <div className="my-4">
              {
                Object.keys(cartItems).length === 0 && (
                  <div className="py-2 text-gray-500 text-center">
                    You don't have any items yet.
                  </div>
                )
              }
              {
                Object.values(cartItems).map(item => (
                  <div key={item.user.id}>
                    <div className="flex items-center justify-between pb-4 border-b border-gray-300 mb-4">
                      <Link href="" className="underline" >
                        {item.user.name}
                      </Link>
                      <div>
                        <form action={route('cart.checkout')} method='post'>
                          <input type="hidden" name="_token" value={csrf_token} />
                          <input type="hidden" name="vendor_id" value={item.user.id} />

                          <button type="submit" className="btn btn-sm  btn-ghost">
                            <CreditCardIcon className='size-6' />
                            Pay Only for this seller
                          </button>
                        </form>
                      </div>
                    </div>

                    {
                      item.items.map((item) => (
                        <code>
                          {JSON.stringify(item, undefined, 2)}
                        </code>

                        // <CartItem key={item.id}>

                        // </CartItem>
                      ))
                    }
                  </div>
                ))
              }
            </div>
          </div>
        </div>
        <div className="card flex-1 bg-white dark:bg-gray-800 order-2 lg:order-1 lg:min-w-[260px]">
          <div className="card-body">
            Subtotal ({totalCartQuantity} items): &nbsp;
            <CurrencyFormatter amount={totalCartPrice} currency='PKR' local='PK' />
            <form action={route('cart.checkout')} method='post'>
              <input type="hidden" name="_token" value={csrf_token} />

              <Button type="submit" className="rouded-full">
                <CreditCardIcon className='size-6' />
                Proceed to Checkout
              </Button>
            </form>
          </div>
        </div>
      </div>

    </AppHeaderLayout >
  );
}
