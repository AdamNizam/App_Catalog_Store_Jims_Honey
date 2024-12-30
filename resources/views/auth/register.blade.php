<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="{{asset('output.css')}}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    </head>
    <body>
        <div class="relative flex flex-col w-full max-w-[640px] min-h-screen gap-5 mx-auto bg-[#F5F5F0]">

            <div class="flex flex-col items-center justify-center px-4 gap-[30px] my-auto"></div>
            <div class="flex flex-col items-center justify-center px-4 gap-[30px] my-auto">
                <div class="flex w-full max-w-[345px] rounded-[30px] bg-white p-5">
                    <div class="w-full">
                        <!-- Register Form -->
                        <form id="register-form" class="flex flex-col gap-6">
                            <h1 class="font-bold text-2xl leading-4 text-center">Register</h1>
                            <div class="flex flex-col gap-2">
                                <label for="name" class="font-semibold leading-[21px]">Name</label>
                                <input style="padding: 16px 20px; font-size: 16px;" type="text" name="name" id="name" class="w-full rounded-full px-4 py-2 bg-[#F8F8F9] font-semibold leading-[21px] placeholder:font-normal" placeholder="Enter your name">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="name" class="font-semibold leading-[21px]">Adress</label>
                                <input style="padding: 16px 20px; font-size: 16px;" type="text" name="name" id="name" class="w-full rounded-full px-4 py-2 bg-[#F8F8F9] font-semibold leading-[21px] placeholder:font-normal" placeholder="Enter your name">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="name" class="font-semibold leading-[21px]">Code Pose</label>
                                <input style="padding: 16px 20px; font-size: 16px;" type="text" name="name" id="name" class="w-full rounded-full px-4 py-2 bg-[#F8F8F9] font-semibold leading-[21px] placeholder:font-normal" placeholder="Enter your name">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="email" class="font-semibold leading-[21px]">Email</label>
                                <input style="padding: 16px 20px; font-size: 16px;" type="email" name="email" id="email" class="w-full rounded-full px-4 py-2 bg-[#F8F8F9] font-semibold leading-[21px] placeholder:font-normal" placeholder="Enter your email">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="password" class="font-semibold leading-[21px]">Password</label>
                                <input style="padding: 16px 20px; font-size: 16px;" type="password" name="password" id="password" class="w-full rounded-full px-4 py-2 bg-[#F8F8F9] font-semibold leading-[21px] placeholder:font-normal" placeholder="Enter your password">
                            </div>

                            <button type="submit" class="rounded-full p-[12px_20px] text-center w-full bg-[#C5F277] font-bold">Register</button>

                            <div class="flex flex-col gap-2">
                                <p class="text-center text-gray-500">
                                    Do you Have Account?
                                    <a href="/login/customer" class="text-blue-500 hover:underline" style="color: #82c809;">Login</a>
                                </p>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-center justify-center px-4 gap-[30px] my-auto"></div>
        </div>
    </body>
</html>
