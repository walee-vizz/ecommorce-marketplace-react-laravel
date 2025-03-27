import Carousel from "@/components/ui/carousel";
import CurrencyFormatter from "@/components/ui/currency-formatter";
import { arraysAreEqual } from "@/helpers";
import AppHeaderLayout from "@/layouts/app/app-header-layout";
import { Product, VariationTypeOption } from "@/types";
import { Head, router, useForm, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";

export default function Show({ product, variationOptions }: { product: Product, variationOptions: number[] }) {
  // console.log('Product :', product);
  // console.log('Product variations :', variationOptions);

  const form = useForm<{
    quantity: number;
    option_ids: Record<string, number>;
    price: number | null;
  }>({
    quantity: 1,
    option_ids: {},
    price: null,
  });

  const { url } = usePage();

  const [selectedOptions, setSelectedOptions] = useState<Record<number, VariationTypeOption>>([]);

  const images = useMemo(() => {

    for (const typeId in selectedOptions) {
      const option = selectedOptions[typeId];
      if (option.images?.length && option.images?.length > 0) return option.images;
    }
    return product.images;

  }, [product, selectedOptions]);


  const computedProduct = useMemo(() => {
    const selectedOptionIds = Object.values(
      selectedOptions)
      ?.map((op) => op.id)
      .sort();

    for (const variation of product.variations) {
      const optionIds = variation.variation_type_option_ids.sort();

      if (arraysAreEqual(optionIds, selectedOptionIds)) {
        return {
          price: variation.price,
          quantity: variation.quantity || Number.MAX_VALUE,
        };
      }
    }

    return {
      price: product.price,
      quantity: product.stock,
    };
  }, [product, selectedOptions]);


  useEffect(() => {

    for (const type of product.variationTypes) {

      const selectedOptionId = variationOptions?.[type.id];
      if (selectedOptionId) {
        const option = type.options.find((op) => op.id == selectedOptionId) || type.options[0];
        chooseOption(type.id, option, false);

      }
    }


  }, []);



  const getOptionIdsMap = (newOptions: object) => {

    return Object.fromEntries(
      Object.entries(newOptions).map(([a, b]) => [a, b.id])
    );
  }


  const chooseOption = (typeId: number, option: VariationTypeOption, updateRouter: boolean = true) => {

    setSelectedOptions((prevSelectedOptions) => {
      const newOptions = { ...prevSelectedOptions, [typeId]: option };

      if (updateRouter) {
        router.get(url, {
          options: getOptionIdsMap(newOptions),
        }, {
          preserveScroll: true,
          preserveState: true
        });
      }
      // console.log('updated options:', newOptions);
      // console.log('product v type options:', product.variationTypes);
      return newOptions;
    });

  }


  const onQuantityChange = (e: React.ChangeEvent<HTMLSelectElement>) => {

    form.setData('quantity', parseInt(e.target.value));
  }

  const onAddToCart = () => {
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


  const renderProductVariationTypes = () => {

    return (

      product.variationTypes.map((type, i) => (
        <div key={i} >
          <b>{type.name}</b>
          {type.type == 'Image' &&
            <div className="flex gap-2 mb-4">
              {type.options.map((option, j) => (
                <div key={j} onClick={() => {
                  chooseOption(type.id, option);
                }}>
                  {
                    option.images &&
                    <img src={option.images[0].thumb} alt="" className={"w-[50px] h-[60px] " + (selectedOptions[type.id]?.id === option.id ? 'outline-4 outline-primary' : '')} />
                  }
                </div>
              ))}
            </div>
          }
          {type.type == 'Radio' &&

            <div className="flex join">
              {type.options.map((option) => (
                <input
                  onChange={() => chooseOption(type.id, option)}
                  type="radio"
                  key={option.id}
                  className={`join-item btn ${selectedOptions[type.id]?.id === option.id ? 'btn-primary' : ''}`}
                  value={option.id}
                  checked={selectedOptions[type.id]?.id === option.id}
                  name={'variation_type_' + type.id}
                  aria-label={option.name}
                />
              ))}
            </div>

          }
        </div>
      ))
    );
  }


  const renderAddToCartButton = () => {
    return (
      <div className="mb-8 flex gap-4">
        <select value={form.data.quantity}
          onChange={onQuantityChange}
          className="select select-bordered w-full"
        >
          {
            Array.from({
              length: Math.min(10, computedProduct.quantity)
            }).map((el, i) => (
              <option value={i + 1} key={i}>Quantity: {i + 1}</option>
            ))
          }
        </select>
        <button onClick={onAddToCart} className="btn btn-primary">
          Add to Cart
        </button>
      </div>
    );

  }

  useEffect(() => {
    const idsMap = Object.fromEntries(
      Object.entries(selectedOptions).map(([typeId, option]) => [typeId, option.id])
    );

    form.setData('option_ids', idsMap);

  }, [selectedOptions]);

  return (
    // <div>
    //   Show Page {product.title}
    // </div>
    <AppHeaderLayout>
      <Head title={product.title} >
      </Head>

      <div className="container mx-auto p-8">
        <div className="grid gap-8 grid-cols-1 lg:grid-cols-12">
          <div className="col-span-12 md:col-span-7">
            <Carousel images={images} />
          </div>
          <div className="col-span-12 md:col-span-5">
            <h1 className="text-2xl mb-8">
              {product.title}
            </h1>
            <div>
              <div className="text-3xl font-semibold">
                <CurrencyFormatter amount={computedProduct.price} currency="PKR" local="PK" />
              </div>
            </div>

            {/* <pre>
              {JSON.stringify(product.variationTypes, undefined, 2)}
            </pre> */}
            {renderProductVariationTypes()}

            {
              // computedProduct.quantity != undefined && computedProduct.quantity > 10 &&
              <div className="text-error my-4">
                <span>Only {computedProduct.quantity} left</span>
              </div>
            }

            {renderAddToCartButton()}

            <b className="text-xl">About the item</b>
            <div className="wysiwyg-output" dangerouslySetInnerHTML={{ __html: product.description }} />
          </div>
        </div>
      </div>
    </AppHeaderLayout >
  );
}

