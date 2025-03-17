import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppHeaderLayout from '@/layouts/app/app-header-layout';

interface ResetPasswordProps {
  token: string;
  email: string;
}

type ResetPasswordForm = {
  token: string;
  email: string;
  password: string;
  password_confirmation: string;
};

export default function ResetPassword({ token, email }: ResetPasswordProps) {
  const { data, setData, post, processing, errors, reset } = useForm<Required<ResetPasswordForm>>({
    token: token,
    email: email,
    password: '',
    password_confirmation: '',
  });

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    post(route('password.store'), {
      onFinish: () => reset('password', 'password_confirmation'),
    });
  };

  return (
    <AppHeaderLayout>
      <Head title="Reset password" />
      <div className='p-8'>
        <div className="card bg-white  shadow max-w-[420px] mx-auto">
          <div className="card-body">
            <form onSubmit={submit}>
              <div className="grid gap-6">
                <div className="grid gap-2">
                  <Label htmlFor="email">Email</Label>
                  <Input
                    id="email"
                    type="email"
                    name="email"
                    autoComplete="email"
                    value={data.email}
                    className="mt-1 block w-full"
                    readOnly
                    onChange={(e) => setData('email', e.target.value)}
                  />
                  <InputError message={errors.email} className="mt-2" />
                </div>

                <div className="grid gap-2">
                  <Label htmlFor="password">Password</Label>
                  <Input
                    id="password"
                    type="password"
                    name="password"
                    autoComplete="new-password"
                    value={data.password}
                    className="mt-1 block w-full"
                    autoFocus
                    onChange={(e) => setData('password', e.target.value)}
                    placeholder="Password"
                  />
                  <InputError message={errors.password} />
                </div>

                <div className="grid gap-2">
                  <Label htmlFor="password_confirmation">Confirm password</Label>
                  <Input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    autoComplete="new-password"
                    value={data.password_confirmation}
                    className="mt-1 block w-full"
                    onChange={(e) => setData('password_confirmation', e.target.value)}
                    placeholder="Confirm password"
                  />
                  <InputError message={errors.password_confirmation} className="mt-2" />
                </div>

                <Button type="submit" className="mt-4 w-full" disabled={processing}>
                  {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                  Reset password
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AppHeaderLayout>
  );
}
