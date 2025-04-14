
import CurrencyFormatter from "@/components/ui/currency-formatter";
import { CheckCircleIcon } from "@heroicons/react/24/outline";
import AppHeaderLayout from "@/layouts/app/app-header-layout";
import { SharedData, OrdersList } from "@/types";
import { Head, Link } from "@inertiajs/react";

export default function Success({
  orders
}: SharedData & {
  orders: OrdersList
}) {
  console.log('order :', orders);
  return (
    <AppHeaderLayout>
      <Head title="Payment was Completed" />
      <div className="w-[480px] py-8 mx-auto px-4">

        <div className="flex flex-col items-center justify-center">
          <div className="text-6xl text-emerald-600">
            <CheckCircleIcon className={"size-24"} />
          </div>
          <div className="text-3xl font-bold">
            Payment was Completed!
          </div>
        </div>
        <div className="my-6 text-lg">
          Thank you for your purchase. Your payment was completed successfully.
        </div>
        {orders?.data.map(order => (
          <div key={order?.id} className="bg-white dark:bg-gray-800 rouded-lg p-6 mb-4 flex flex-col gap-2">
            <h3 className="text-3xl mb-3">Order Summary</h3>
            <div className="flex justify-between mb-2 font-bold">
              <div className="text-gary-400">
                Seller
              </div>
              <div>
                <Link href={"#"} className="hover:underline" >
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
            <div className="flex justify-between mb-4">
              <Link href={"#"} className="btn btn-primary" >
                View order Details
              </Link>
              <Link href={route('dashboard')} className="btn" >
                Back Home
              </Link>
            </div>
          </div>
        ))}
      </div>
    </AppHeaderLayout>
  );
}
