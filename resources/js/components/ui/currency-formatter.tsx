// import React from "react";

export default function CurrencyFormatter(
  {
    amount,
    currency = 'USD',
    local }
    :
    {
      amount: number,
      currency: string,
      local: string
    }) {
  return new Intl.NumberFormat(local, {
    style: 'currency',
    currency: currency,
  }).format(amount);

}
