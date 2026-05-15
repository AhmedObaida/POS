@extends('layouts.admin')

@section('title', 'Record payment')

@section('content')
<h1 class="h3 mb-3">Record payment</h1>
<div class="card shadow-sm" style="max-width: 720px;">
    <div class="card-body">
        <form method="post" action="{{ route('admin.payments.store') }}" id="paymentForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Customer</label>
                <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                    <option value="">— Select —</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ (string) old('customer_id', $selectedCustomerId) === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Invoice (unpaid / partial)</label>
                <select name="invoice_id" id="invoice_id" class="form-select @error('invoice_id') is-invalid @enderror" required>
                    <option value="">— Select customer first —</option>
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}" {{ (string) old('invoice_id', $selectedInvoiceId) === (string) $inv->id ? 'selected' : '' }}>
                            {{ $inv->invoice_number }} — remaining {{ number_format($inv->remaining_amount, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('invoice_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Payment method</label>
                <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                    @php $m = old('payment_method', 'cash'); @endphp
                    <option value="cash" {{ $m === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="card" {{ $m === 'card' ? 'selected' : '' }}>Card</option>
                    <option value="transfer" {{ $m === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="other" {{ $m === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Payment date</label>
                <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Save payment</button>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var customer = document.getElementById('customer_id');
    var invoice = document.getElementById('invoice_id');
    var url = "{{ route('admin.payments.open-invoices') }}";

    function loadInvoices() {
        var cid = customer.value;
        if (!cid) {
            invoice.innerHTML = '<option value="">— Select customer first —</option>';
            return;
        }
        invoice.innerHTML = '<option value="">Loading…</option>';
        fetch(url + '?customer_id=' + encodeURIComponent(cid), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) { return r.json(); }).then(function (data) {
            invoice.innerHTML = '<option value="">— Select invoice —</option>';
            data.forEach(function (inv) {
                var opt = document.createElement('option');
                opt.value = inv.id;
                opt.textContent = inv.invoice_number + ' — remaining ' + parseFloat(inv.remaining_amount).toFixed(2);
                invoice.appendChild(opt);
            });
        }).catch(function () {
            invoice.innerHTML = '<option value="">Failed to load</option>';
        });
    }

    customer.addEventListener('change', loadInvoices);
    if (customer.value) {
        var hadServerList = {{ $invoices->count() > 0 ? 'true' : 'false' }};
        if (!hadServerList) {
            loadInvoices();
        }
    }
})();
</script>
@endpush
@endsection
