<div>


    <form class="max-w-sm mx-auto p-4">
        <div class="mb-5">
            <label for="ip" class="block mb-2 text-sm font-medium text-gray-900 ">Your Router's IP Address</label>
            <input type="text" wire:model.live="ip" id="ip"
                   class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                   placeholder="1.1.1.1" required/>
            @error('ip') <span class="error text-red-900">{{ $message }}</span> @enderror
        </div>
        <div class="mb-5">
            <label for="port" class="block mb-2 text-sm font-medium text-gray-900 ">Your Router's Port</label>
            <input type="number" wire:model.live="port" id="port"
                   class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                   placeholder="8291"/>
            @error('port') <span class="error text-red-900">{{ $message }}</span> @enderror
        </div>
        <div class="mb-5">
            <label for="username" class="block mb-2 text-sm font-medium text-gray-900 ">Username</label>
            <input type="text" wire:model.live="username" id="username"
                   class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                   required/>
            @error('username') <span class="error text-red-900">{{ $message }}</span> @enderror
        </div>
        <div class="mb-5">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 ">Password</label>
            <input type="password" wire:model.live="password" id="password"
                   class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                   required/>
            @error('password') <span class="error text-red-900">{{ $message }}</span> @enderror
        </div>
        <div class="mb-5">
            <label for="versions" class="block mb-2 text-sm font-medium text-gray-900 ">Select a RouterOS
                version</label>
            <select id="versions" name="versions" wire:change="setVersion($event.target.value)"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                <option value="0" selected>Choose a version</option>
                <option value="7.17">7.17</option>
                <option value="7.16">7.16</option>
                <option value="7.15">7.15</option>
                <option value="7.14">7.14</option>
                <option value="7.13">7.13</option>
                <option value="7.12">7.12</option>
                <option value="7.11">7.11</option>
                <option value="7.10">7.10</option>
                <option value="7.9">7.9</option>
                <option value="7.8">7.8</option>
                <option value="7.7">7.7</option>
                <option value="7.6">7.6</option>
                <option value="7.5">7.5</option>
                <option value="7.4">7.4</option>
                <option value="7.3">7.3</option>
                <option value="7.2">7.2</option>
                <option value="7.1">7.1</option>
                <option value="6.49.5">6.49.5</option>
                <option value="6.48.6">6.48.6</option>
            </select>
            @error('version') <span class="error text-red-900">{{ $message }}</span> @enderror
        </div>
        <div class="mb-5">
            <label for="audit-options" class="block mb-2 text-sm font-medium text-gray-900">Select Audit Options</label>
            <div class="flex items-start mb-2">
                <input id="firewall" wire:model="selected" type="checkbox" value="firewall"
                       class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300">
                <label for="firewall" class="ml-2 text-sm font-medium text-gray-900">Firewall</label>
            </div>
            <div class="flex items-start mb-2">
                <input id="nat" type="checkbox" wire:model="selected" value="nat"
                       class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300">
                <label for="nat" class="ml-2 text-sm font-medium text-gray-900">NAT</label>
            </div>
            <div class="flex items-start mb-2">
                <input id="services" type="checkbox" wire:model="selected" value="services"
                       class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300">
                <label for="services" class="ml-2 text-sm font-medium text-gray-900">Services</label>
            </div>
            <div class="flex items-start mb-2">
                <input id="vpn" type="checkbox" wire:model="selected" value="vpn"
                       class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300">
                <label for="vpn" class="ml-2 text-sm font-medium text-gray-900">VPN</label>
            </div>
            @error('selected') <span class="error text-red-900">{{ $message }}</span> @enderror
        </div>
        <div class="flex items-start mb-5">
            <label for="terms" class="ms-2 text-sm font-medium text-gray-900 ">This site is only for demo purposes, we
                are not storing ANY DATA!</label>
        </div>
        <button wire:click="submitForm" type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">
            Go!
        </button>
    </form>

    <div wire:loading.class.remove="hidden" wire:target="submitForm"
         class="hidden max-w-sm mx-auto p-4 flex items-center flex-wrap justify-center gap-3 mb-5 border border-gray-300 rounded bg-gray-50 rounded-md">
        <p>Audit is running</p>
        <i class="fa-spin fa fa-spinner"></i>
    </div>


    @if($this->firewallAuditRunning)
        <div x-data="{open: false}"
             class="max-w-sm mx-auto p-4 flex items-center flex-wrap justify-between gap-3 mb-5 border border-gray-300 rounded bg-gray-50 rounded-md">
            <div class="cursor-pointer" x-on:click="open = !open">Firewall Audit</div>
            <div
                class="icon w-8 h-8 rounded-full {{$this->auditResult["firewall"][0] == [] ? 'bg-green-700':'bg-red-700'}} flex items-center justify-center text-white">
                <i class="fa-solid {{$this->auditResult["firewall"][0] == [] ? 'fa-check':'fa-close'}}"></i>
            </div>
            <div class="basis-full" x-show="open">

                @foreach($this->auditResult["firewall"] as $results)

                    @if($results != [])
                        @if(array_key_exists('error', $results))
                            <div class="text-sm font-medium text-red-900">{{$results['message']}}</div>
                        @else
                            @foreach($results as $result)
                                <div class="text-sm font-medium text-red-900">{{$result['reason']}} at
                                    id {{$result["rule"]}}</div>
                            @endforeach
                        @endif
                    @endif
                @endforeach

            </div>
        </div>
    @endif

    @if($this->natAuditRunning)
        @ray($this->auditResult)
        <div x-data="{open: false}"
             class="max-w-sm mx-auto p-4 flex items-center flex-wrap justify-between gap-3 mb-5 border border-gray-300 rounded bg-gray-50 rounded-md">
            <div class="cursor-pointer" x-on:click="open = !open">Nat Audit</div>
            <div
                class="icon w-8 h-8 rounded-full {{$this->auditResult["nat"][0] == [] ? 'bg-green-700':'bg-red-700'}} flex items-center justify-center text-white">
                <i wire:loading.class.remove="fa-solid" wire:loading.class="fa-spin"
                   class="fa-solid  {{$this->auditResult["nat"][0] == [] ? 'fa-check':'fa-close'}}"></i>
            </div>
            <div class="basis-full" x-show="open">

                @foreach($this->auditResult["nat"] as $results)

                    @if($results != [])
                        @if(array_key_exists('error', $results))
                            <div class="text-sm font-medium text-red-900">{{$results['message']}}</div>
                        @else
                            @foreach($results as $result)
                                <div class="text-sm font-medium text-red-900">{{$result['reason']}} at
                                    id {{$result["rule"]}}</div>
                            @endforeach
                        @endif
                    @endif
                @endforeach

            </div>
        </div>
    @endif
</div>
