// import React from "react";

import { DefaultCurrency, DefaultCurrencyLocal } from "@/constants";

export default function CurrencyFormatter(
  {
    amount,
    currency = DefaultCurrency,
    local = DefaultCurrencyLocal }
    :
    {
      amount: number,
      currency?: string,
      local?: string
    }) {
  return new Intl.NumberFormat(local, {
    style: 'currency',
    currency: currency,
  }).format(amount);

}
