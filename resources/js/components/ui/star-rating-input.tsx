import * as React from "react"

import { cn } from "@/lib/utils"

const StarRatingInput = ({
  className,
  value = 0,
  ...props
}: { value: number } & React.ComponentProps<"input">) => {
  return (
    <div
      className={cn("rating rating-xs", className)}
    >
      {[1, 2, 3, 4, 5].map((star) => (
        <input
          key={star}
          type="radio"
          className="mask mask-star-2 bg-orange-400"
          aria-label={`${star} star`}
          value={star}
          defaultChecked={value === star}
          {...props}
        />
      ))}
    </div>
  )
}

export default StarRatingInput
