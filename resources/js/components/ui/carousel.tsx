import { Image } from "@/types";


export default function Carousel({ images }: { images: Image[] }) {
  return (
    <>
      <div className="flex items-start gap-8">
        <div className="flex flex-col items-center gap-2 py-2">
          {images.map((image: Image, index: number) => (
            <a href={'#item' + index} className="border-2 hover:border-blue-500" key={index}>
              <img src={image.thumb} alt="" className="w-[50px]" />
            </a>
          )
          )
          }

        </div>
        <div className="carousel w-full">
          {images.map((image: Image, index: number) => (

            <div id={'item' + index} className="carousel-item w-full" key={index}>
              <img
                src={image.large}
                className="w-full" />
            </div>
          ))}

        </div>
      </div>
    </>
  );
}
