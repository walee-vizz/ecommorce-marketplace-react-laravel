import { Image } from "@/types";
import { useEffect, useState } from 'react';


export default function Carousel({ images }: { images: Image[] }) {
  const [selectedItem, setSelectedItem] = useState<Image>(images[0]);

  useEffect(() => {
    setSelectedItem(images[0]);
  }, [images])

  return (
    <>
      <div className="flex items-start gap-8">
        <div className="flex flex-col items-center gap-2 py-2">
          {images.map((image: Image, index: number) => (
            <button className={'border-2 cursor-pointer hover:border-blue-500  ' + (selectedItem.id === image.id  ? 'border-blue-500' : '')}  key={index}
            onClick={() => setSelectedItem(image)}
            >
              <img src={image.thumb} alt="" className="w-[50px]" />
            </button>
          )
          )
          }

        </div>
        <div className="carousel w-full">
          {
            selectedItem &&
            <div  className="carousel-item w-full">
              <img
                src={selectedItem.large} alt='product image'
                className="w-full" />
            </div>
          }
        </div>
      </div>
    </>
  );
}
