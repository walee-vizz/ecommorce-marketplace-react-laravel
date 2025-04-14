import CurrencyFormatter from "@/components/ui/currency-formatter";
import { CreditCardIcon, XCircleIcon } from "@heroicons/react/24/outline";
import AppHeaderLayout from "@/layouts/app/app-header-layout";
import { SharedData, OrdersList } from "@/types";
import { Head, Link } from "@inertiajs/react";
import { Button } from "@/components/ui/button";

export default function Failure({
  orders, csrf_token
}: SharedData & {
  orders: OrdersList
}) {
  console.log('Failed orders:', orders);

  return (
    <AppHeaderLayout>
      <Head title="Payment Failed" />
      <div className="w-[480px] py-8 mx-auto px-4">
        <div className="flex flex-col items-center justify-center">
          <div className="text-6xl text-red-600">
            <XCircleIcon className="w-24 h-24" />
          </div>
          <div className="text-3xl font-bold mt-4">
            Payment Failed
          </div>
        </div>
        <div className="my-6 text-lg text-center">
          Unfortunately, your payment could not be processed. Please try again or contact support if the issue persists.
        </div>
        {orders?.data.length > 0 ? (
          orders.data.map(order => (
            <div key={order?.id} className="bg-white dark:bg-gray-800 rounded-lg p-6 mb-4 flex flex-col gap-2">
              <h3 className="text-2xl mb-3 font-semibold">Order Summary</h3>
              <div className="flex justify-between mb-2 font-bold">
                <div className="text-gray-400">
                  Seller
                </div>
                <div>
                  <Link href={"#"} className="hover:underline">
                    {order.vendorUser.store_name}
                  </Link>
                </div>
              </div>
              <div className="flex justify-between mb-2">
                <div className="text-gray-400">
                  Order Number
                </div>
                <div>
                  <Link href={"#"} className="hover:underline">
                    #{order.id}
                  </Link>
                </div>
              </div>
              <div className="flex justify-between mb-3">
                <div className="text-gray-400">
                  Items
                </div>
                <div>
                  {order.orderItems?.length}
                </div>
              </div>
              <div className="flex justify-between mb-3">
                <div className="text-gray-400">
                  Total
                </div>
                <div>
                  <CurrencyFormatter amount={order.totalPrice} />
                </div>
              </div>
              <div className="flex justify-between mt-4">
                <form action={route('cart.checkout')} method='post'>
                  <input type="hidden" name="_token" value={csrf_token} />

                  <Button type="submit" className="rouded-full">
                    <CreditCardIcon className='size-6' />
                    Retry Payment
                  </Button>
                </form>
                {/* <Link href={"#"} className="btn btn-primary">
                  Retry Payment
                </Link> */}
                <Link href={route('dashboard')} className="btn">
                  Back Home
                </Link>
              </div>
            </div>
          ))
        ) : (
          <div className="text-center text-gray-500">
            No orders found.
          </div>
        )}
      </div>
    </AppHeaderLayout>
  );
}
