
import React, { useState } from 'react';
import { Link, router, useForm } from '@inertiajs/react';
import { CartItem as CartItemType } from '@/types';
import CurrencyFormatter from '@/components/ui/currency-formatter';
import { productRoute } from '@/helpers';
import { Button } from '../ui/button';
import { Input } from '../ui/input';


export default function CartItem({ item }: { item: CartItemType }) {

  const [error, setError] = useState('');
  const deleteForm = useForm({
    option_ids: item.option_ids,

  });

  const onDeleteClick = () => {
    deleteForm.delete(route('cart.destroy', item.product_id), {
      preserveScroll: true,

    })
  }

  const handleQuantityChange = (e: React.ChangeEvent<HTMLInputElement>) => {

    setError('');

    router.put(route('cart.update', item.product_id), {
      quantity: e.target.value,
      option_ids: item.option_ids,
    }, {
      preserveScroll: true,
      onError: (errors) => {
        setError(Object.values(errors)[0]);
      },
      onSuccess: () => {
        setError('');

      }
    })
  }

  return (
    <>
      <div key={item.id} className='flex gap-6 p-3 '>
        <Link href={productRoute(item)} className='w-32 min-w-32 min-h-32 flex justify-center self-start'  >
          <img src={item.image} alt={item.title} className='max-w-full max-h-full' />
        </Link>
        <div className="flex-1 flex flex-col">
          <div className="flex-1">
            <h3 className="mb-3 text-sm font-semibold hover:underline">
              <Link href={productRoute(item)} >
                {item.title}
              </Link>
            </h3>
            <div className="text-xs">

              {
                item.options.map((option) => (
                  <div key={option.id}>
                    <strong className='text-bold'> {option.type.name}: </strong>
                    {option.name}
                  </div>
                ))
              }
            </div>
          </div>

          <div className="flex gap-2 justify-between items-center mt-4">
            <div className="text-sm">
              Quantity:
            </div>
            <div className={error ? 'tooltip tooltip-error tooltip-top' : ''} data-tip={error}>
              <Input type="number" defaultValue={item.quantity} min={1} className="input-sm w-16" onChange={handleQuantityChange} />
            </div>
            <Button onClick={(e) => onDeleteClick()} className="btn btn-sm btn-ghost underline text-error">Delete</Button>
            <Button className="btn btn-sm btn-ghost underline ">Save for Later</Button>
            <div className="font-bold text-lg">
              <CurrencyFormatter amount={(item.price * item.quantity)} />
            </div>
          </div>
        </div>
      </div>
      <div className="divider">

      </div>
    </>
  );
}
